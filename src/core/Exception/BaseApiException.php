<?php

declare(strict_types=1);

namespace Core\Exception;

use Exception;

abstract class BaseApiException extends Exception
{
    abstract public function getStatusCode(): int;
    abstract public function getErrorMessage(): string;
}
