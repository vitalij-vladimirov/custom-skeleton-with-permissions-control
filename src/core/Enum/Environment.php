<?php

declare(strict_types=1);

namespace Core\Enum;

use Core\Service\Enum;

class Environment extends Enum
{
    public const LOCAL = 'local';
    public const STAGING = 'staging';
    public const PRODUCTION = 'production';
}
