<?php

declare(strict_types=1);

namespace App\Factory;

use DB\Entity\Role;
use DB\Entity\User;
use DB\Entity\UserRole;

class UserRoleFactory
{
    public function create(User $user, Role $role): UserRole
    {
        $userRole = new UserRole();
        $userRole->userId = $user->id;
        $userRole->roleId = $role->id;

        return $userRole;
    }
}
