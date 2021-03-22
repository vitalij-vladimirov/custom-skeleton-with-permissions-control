<?php

declare(strict_types=1);

namespace App\Service\Permission;

use App\Factory\UserRoleFactory;
use DB\Entity\User;
use DB\Entity\UserRole;
use DB\Repository\RoleRepository;

class UserRoleManager
{
    private RoleRepository $roleRepository;
    private UserRoleFactory $userRoleFactory;

    public function __construct(RoleRepository $roleRepository, UserRoleFactory $userRoleFactory)
    {
        $this->roleRepository = $roleRepository;
        $this->userRoleFactory = $userRoleFactory;
    }

    public function create(User $user, string $identifier): ?UserRole
    {
        $role = isUuid($identifier)
            ? $this->roleRepository->getOneByUuid($identifier)
            : $this->roleRepository->getOneByIdentifier($identifier);

        if ($role === null) {
            return null;
        }

        $userRole = $this->userRoleFactory->create($user, $role);

        return $userRole->save();
    }
}
