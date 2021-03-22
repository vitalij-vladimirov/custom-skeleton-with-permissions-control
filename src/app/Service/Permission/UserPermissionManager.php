<?php

declare(strict_types=1);

namespace App\Service\Permission;

use App\Factory\UserPermissionFactory;
use DB\Entity\User;
use DB\Entity\UserPermission;
use DB\Repository\UserPermissionRepository;

class UserPermissionManager
{
    private UserPermissionFactory $userPermissionFactory;
    private UserPermissionRepository $userPermissionRepository;

    public function __construct(
        UserPermissionFactory $userPermissionFactory,
        UserPermissionRepository $userPermissionRepository
    ) {
        $this->userPermissionFactory = $userPermissionFactory;
        $this->userPermissionRepository = $userPermissionRepository;
    }

    public function create(User $user, string $permission, bool $grant): UserPermission
    {
        $userPermission = $this->userPermissionFactory->create($user, $permission, $grant);

        return $this->userPermissionRepository->save($userPermission);
    }
}
