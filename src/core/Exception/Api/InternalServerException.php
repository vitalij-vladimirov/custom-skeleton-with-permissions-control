<?php

declare(strict_types=1);

namespace Core\Exception\Api;

use Core\Exception\BaseApiException;
use Core\Enum\ResponseCode;

class InternalServerException extends BaseApiException
{
    public function getStatusCode(): int
    {
        return ResponseCode::INTERNAL_SERVER_EXCEPTION;
    }

    public function getErrorMessage(): string
    {
        return 'Internal server exception';
    }
}
