<?php

declare(strict_types=1);

namespace App\Exception\Api;

use App\Exception\BaseApiException;
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
