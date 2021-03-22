<?php

declare(strict_types=1);

namespace App\Service\Permission;

use App\Factory\UserPermissionFactory;
use DB\Entity\User;
use DB\Entity\UserPermission;

class UserPermissionManager
{
    private UserPermissionFactory $userPermissionFactory;

    public function __construct(UserPermissionFactory $userPermissionFactory)
    {
        $this->userPermissionFactory = $userPermissionFactory;
    }

    public function create(User $user, string $permission, bool $grant): UserPermission
    {
        $userPermission = $this->userPermissionFactory->create($user, $permission, $grant);

        return $userPermission->save();
    }
}
