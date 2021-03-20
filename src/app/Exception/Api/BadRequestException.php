<?php

declare(strict_types=1);

namespace App\Exception\Api;

use App\Exception\BaseApiException;
use Core\Enum\ResponseCode;

class BadRequestException extends BaseApiException
{
    public function getStatusCode(): int
    {
        return ResponseCode::BAD_REQUEST;
    }

    public function getErrorMessage(): string
    {
        return 'Bad request';
    }
}
