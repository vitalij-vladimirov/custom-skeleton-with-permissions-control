<?php

declare(strict_types=1);

namespace DB\Repository;

use DB\Entity\User;
use DB\Entity\UserRole;

class UserRoleRepository
{
    /**
     * @param User $user
     *
     * @return UserRole[]
     */
    public function getByUser(User $user): array
    {
        return UserRole::query()
            ->whereQuery(sprintf('user_id = \'%s\'', $user->id))
            ->get();
    }
}
