<?php

declare(strict_types=1);

namespace DB\Entity;

use Core\Entity\Database\BaseDbEntity;
use DateTimeImmutable;

class RolePermission extends BaseDbEntity
{
    public ?int $id;
    public ?string $uuid;
    public int $roleId;
    public string $permission;
    public ?DateTimeImmutable $createdAt;
    public ?DateTimeImmutable $updatedAt;

    public function getTable(): string
    {
        return 'role_permission';
    }
}
