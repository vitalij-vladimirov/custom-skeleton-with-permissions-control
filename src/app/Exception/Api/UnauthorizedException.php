<?php

declare(strict_types=1);

namespace App\Exception\Api;

use App\Exception\BaseApiException;
use Core\Enum\ResponseCode;

class UnauthorizedException extends BaseApiException
{
    public function getStatusCode(): int
    {
        return ResponseCode::UNAUTHORIZED;
    }

    public function getErrorMessage(): string
    {
        return 'Unauthorized';
    }
}
