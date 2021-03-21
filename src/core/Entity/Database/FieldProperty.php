<?php

declare(strict_types=1);

namespace Core\Entity\Database;

class FieldProperty
{
    public string $camelCaseName;
    public string $snakeCaseName;
    public string $type;
    public bool $isNullable;
}
