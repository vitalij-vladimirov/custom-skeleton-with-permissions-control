<?php

declare(strict_types=1);

namespace Core\Enum;

use Core\Service\Enum;

class HttpMethod extends Enum
{
    public const GET = 'get';
    public const POST = 'post';
    public const PUT = 'put';
    public const PATCH = 'patch';
    public const DELETE = 'delete';
    public const OPTIONS = 'options';

    public static function getAllowMethods(): array
    {
        return [
            self::GET,
            self::POST,
            self::PUT,
            self::PATCH,
            self::DELETE,
            self::OPTIONS,
        ];
    }

    public static function isIdentifierAllowed(): bool
    {
        return in_array(
            strtolower($_SERVER['REQUEST_METHOD']),
            [
                self::GET,
                self::PUT,
                self::PATCH,
                self::DELETE,
                self::OPTIONS,
            ],
            true
        );
    }
}
