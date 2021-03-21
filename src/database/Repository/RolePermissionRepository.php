<?php

declare(strict_types=1);

namespace DB\Repository;

use DB\Entity\RolePermission;
use DB\Entity\UserRole;

class RolePermissionRepository
{
    /**
     * @param UserRole[] $userRoles
     *
     * @return RolePermission[]
     */
    public function getByUserRoles(array $userRoles): array
    {
        if (count($userRoles) === 0) {
            return [];
        }

        $roleIds = [];
        foreach ($userRoles as $role) {
            $roleIds[] = $role->roleId;
        }

        $query = sprintf('role_id IN (\'%s\')', implode('\', \'', $roleIds));

        return RolePermission::query()
            ->whereQuery($query)
            ->get();
    }

    /**
     * @param int[] $roleIds
     *
     * @return RolePermission[]
     */
    private function getByRoleIds(array $roleIds): array
    {
        $query = sprintf('role_id IN (\'%s\')', implode('\', \'', $roleIds));

        return RolePermission::query()
            ->whereQuery($query)
            ->get();
    }
}
