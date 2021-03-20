<?php

declare(strict_types=1);

namespace Core\Entity;

class DatabaseField
{
    public string $camelCaseName;
    public string $snakeCaseName;
    public string $type;
    public bool $isNullable;
}
