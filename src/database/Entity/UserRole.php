<?php

declare(strict_types=1);

namespace DB\Entity;

use Core\Entity\Database\BaseDbEntity;
use DateTimeImmutable;

class UserRole extends BaseDbEntity
{
    public int $id;
    public string $uuid;
    public int $userId;
    public int $roleId;
    public DateTimeImmutable $createdAt;
    public DateTimeImmutable $updatedAt;

    public function getTable(): string
    {
        return 'user_role';
    }
}
