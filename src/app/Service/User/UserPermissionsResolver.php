<?php

declare(strict_types=1);

namespace App\Service\User;

use DB\Entity\RolePermission;
use DB\Entity\User;
use DB\Entity\UserPermission;
use DB\Repository\RolePermissionRepository;
use DB\Repository\UserPermissionRepository;

class UserPermissionsResolver
{
    private UserPermissionRepository $userPermissionRepository;
    private RolePermissionRepository $rolePermissionRepository;

    public function __construct(
        UserPermissionRepository $userPermissionRepository,
        RolePermissionRepository $rolePermissionRepository
    ) {
        $this->userPermissionRepository = $userPermissionRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function getUserPermissions(User $user): array
    {
        $rolePermissions = $this->rolePermissionRepository->getByUser($user);
        $userPermissions = $this->userPermissionRepository->getByUser($user);

        return $this->resolvePermissions($rolePermissions, $userPermissions);
    }

    /**
     * @param RolePermission[] $rolePermissions
     * @param UserPermission[] $userPermissions
     *
     * @return string[]
     */
    private function resolvePermissions(array $rolePermissions, array $userPermissions): array
    {
        $permissions = [];

        if (count($rolePermissions) > 0) {
            foreach ($rolePermissions as $rolePermission) {
                $permission = $rolePermission->permission;
                if (in_array($permission, $permissions, true)) {
                    continue;
                }

                $permissions[] = $permission;
            }
        }

        if (count($userPermissions) === 0) {
            return $permissions;
        }

        foreach ($userPermissions as $userPermission) {
            $permission = $userPermission->permission;
            $grant = $userPermission->grant;

            // User permission
            if ($grant && !in_array($permission, $permissions, true)) {
                $permissions[] = $permission;

                continue;
            }

            // Remove user permission
            if (!$grant && in_array($permission, $permissions, true)) {
                $key = array_search($permission, $permissions, true);
                unset($permissions[$key]);
            }
        }

        return array_values($permissions);
    }
}
