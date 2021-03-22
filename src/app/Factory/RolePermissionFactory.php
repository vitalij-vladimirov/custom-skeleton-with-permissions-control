<?php

declare(strict_types=1);

namespace App\Factory;

use DB\Entity\Role;
use DB\Entity\RolePermission;

class RolePermissionFactory
{
    public function create(Role $role, string $permission): RolePermission
    {
        $rolePermission = new RolePermission();
        $rolePermission->roleId = $role->id;
        $rolePermission->permission = $permission;

        return $rolePermission;
    }
}
