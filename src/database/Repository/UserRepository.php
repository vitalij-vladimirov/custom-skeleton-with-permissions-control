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
}
