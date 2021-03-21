<?php

declare(strict_types=1);

namespace Core\Entity\Database;

use DateTimeImmutable;

class Migration extends BaseDbEntity
{
    public int $version;
    public string $name;
    public DateTimeImmutable $createdAt;

    public function getTable(): string
    {
        return 'migration';
    }
}
