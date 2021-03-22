<?php

declare(strict_types=1);

namespace DB\Repository;

use DB\Entity\User;

class UserRepository
{
    public function getOneByEmail(string $email): ?User
    {
        /** @var User|null $user */
        $user = User::query()
            ->whereQuery(sprintf('email = \'%s\'', $email))
            ->first();

        return $user;
    }

    public function getOneByUuid(string $uuid): ?User
    {
        /** @var User|null $user */
        $user = User::query()
            ->whereQuery(sprintf('uuid = \'%s\'', $uuid))
            ->first();

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User[]
     */
    public function getAllExceptSelf(User $user): array
    {
        return User::query()
            ->whereQuery(sprintf('id <> \'%d\'', $user->id))
            ->orderBy('id')
            ->get();
    }
}
