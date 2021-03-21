<?php

declare(strict_types=1);

namespace Core\Exception\Api;

use Core\Exception\BaseApiException;
use Core\Enum\ResponseCode;

class ForbiddenException extends BaseApiException
{
    public function getStatusCode(): int
    {
        return ResponseCode::FORBIDDEN;
    }

    public function getErrorMessage(): string
    {
        return 'Forbidden';
    }
}
