<?php

declare(strict_types=1);

namespace App\Normalizer\Permission;

use DB\Entity\RolePermission;

class PermissionNormalizer
{
    /**
     * @param RolePermission[] $rolePermissions
     *
     * @return array
     */
    public function normalizeFromRolePermissions(array $rolePermissions): array
    {
        if (count($rolePermissions) === []) {
            return [];
        }

        $permissions = [];
        foreach ($rolePermissions as $rolePermission) {
            $permissions[] = $rolePermission->permission;
        }

        return $permissions;
    }
}
