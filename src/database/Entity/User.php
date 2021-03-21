<?php

declare(strict_types=1);

namespace DB\Entity;

use Core\Entity\Database\BaseDbEntity;
use DateTimeImmutable;

class User extends BaseDbEntity
{
    public ?int $id;
    public ?string $uuid;
    public string $email;
    public string $password;
    public string $firstName;
    public string $lastName;
    public ?DateTimeImmutable $createdAt;
    public ?DateTimeImmutable $updatedAt;

    protected string $test;

    public function getTable(): string
    {
        return 'user';
    }
}
