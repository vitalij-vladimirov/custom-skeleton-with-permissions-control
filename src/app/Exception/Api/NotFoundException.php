<?php

declare(strict_types=1);

namespace App\Exception\Api;

use App\Exception\BaseApiException;
use Core\Enum\ResponseCode;

class NotFoundException extends BaseApiException
{
    public function getStatusCode(): int
    {
        return ResponseCode::NOT_FOUND;
    }

    public function getErrorMessage(): string
    {
        return 'Resource not found';
    }
}
