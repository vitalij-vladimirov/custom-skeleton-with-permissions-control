<?php

declare(strict_types=1);

namespace Core\Enum;

class ResponseCode
{
    public const SUCCESS = 200;
    public const CREATED = 201;
    public const NO_CONTENT = 204;

    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const NOT_FOUND = 404;
    public const INTERNAL_SERVER_EXCEPTION = 500;
}
