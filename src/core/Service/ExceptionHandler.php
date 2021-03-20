<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Exception\Api\InternalServerException;
use Core\Exception\BaseApiException;
use Core\Enum\Environment;
use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $exception): void
    {
        if (!$exception instanceof BaseApiException && ENVIRONMENT === Environment::LOCAL) {
            $this->displayException($exception);

            exit;
        }

        if (!$exception instanceof BaseApiException) {
            $this->logException($exception);
            $exception = new InternalServerException();
        }

        $this->respondWithException($exception);

        exit;
    }

    private function logException(Throwable $exception): void
    {
        // Exception logger can be added here
    }

    private function displayException(Throwable $exception): void
    {
        if (ENVIRONMENT !== Environment::LOCAL) {
            return;
        }

        echo sprintf('<h3>%s</h3>', $exception->getMessage());

        echo '<strong>Trace:</strong>:';
        echo '<ol>';
        echo sprintf('<li>%s:%s</li>', $exception->getFile(), $exception->getLine());

        foreach ($exception->getTrace() as $trace) {
            echo sprintf('<li>%s:%s</li>', $trace['file'], $trace['line']);
        }

        echo '</ol>';
    }

    private function respondWithException(BaseApiException $throwable): void
    {
        header('Content-Type: text/plain charset=UTF-8');
        header('Content-type: application/json');
        http_response_code($throwable->getStatusCode());

        echo json_encode(
            [
                'status' => 'error',
                'message' => $throwable->getErrorMessage(),
            ],
            JSON_THROW_ON_ERROR
        );
    }
}
