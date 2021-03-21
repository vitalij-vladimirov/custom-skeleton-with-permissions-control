<?php

declare(strict_types=1);

namespace DB\Repository;

use DB\Entity\User;
use DB\Entity\UserPermission;

class UserPermissionRepository
{
    /**
     * @param User $user
     * @param bool|null $grant
     *
     * @return UserPermission[]
     */
    public function getByUser(User $user, bool $grant = null): array
    {
        $query = sprintf('user_id = \'%s\'', $user->id);

        if ($grant !== null) {
            $query .= sprintf(' AND `grant` = %d', (int) $grant);
        }

        return UserPermission::query()
            ->whereQuery($query)
            ->get();
    }
}
