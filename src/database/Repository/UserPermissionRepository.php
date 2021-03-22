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
        $query = sprintf('user_id = %d', $user->id);

        if ($grant !== null) {
            $query .= sprintf(' AND `grant` = %d', (int) $grant);
        }

        return UserPermission::query()
            ->whereQuery($query)
            ->orderBy('id')
            ->get();
    }

    public function save(UserPermission $userPermission): UserPermission
    {
        return $userPermission->save();
    }

    public function delete(UserPermission $userPermission): bool
    {
        return $userPermission->delete();
    }
}
