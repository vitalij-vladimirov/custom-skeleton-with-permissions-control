<?php

declare(strict_types=1);

namespace DB\Entity;

use Core\Entity\Database\BaseDbEntity;
use DateTimeImmutable;

class Role extends BaseDbEntity
{
    public int $id;
    public string $uuid;
    public string $identifier;
    public string $title;
    public DateTimeImmutable $createdAt;
    public DateTimeImmutable $updatedAt;

    public function getTable(): string
    {
        return 'role';
    }
}
