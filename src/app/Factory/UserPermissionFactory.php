<?php

declare(strict_types=1);

namespace App\Factory;

use DB\Entity\User;
use DB\Entity\UserPermission;

class UserPermissionFactory
{
    public function create(User $user, string $permission, bool $grant): UserPermission
    {
        $userPermission = new UserPermission();
        $userPermission->userId = $user->id;
        $userPermission->permission = $permission;
        $userPermission->grant = $grant;

        return $userPermission;
    }
}
