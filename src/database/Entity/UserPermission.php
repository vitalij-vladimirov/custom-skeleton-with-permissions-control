<?php

declare(strict_types=1);

namespace DB\Entity;

use Core\Entity\Database\BaseDbEntity;
use DateTimeImmutable;

class UserPermission extends BaseDbEntity
{
    public ?string $uuid;
    public int $userId;
    public string $permission;
    public bool $grant;
    public ?DateTimeImmutable $createdAt;
    public ?DateTimeImmutable $updatedAt;

    public function getTable(): string
    {
        return 'user_permission';
    }
}
