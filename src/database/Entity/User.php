<?php

declare(strict_types=1);

namespace DB\Entity;

use Core\Entity\DbEntity;
use DateTimeImmutable;

class User extends DbEntity
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
