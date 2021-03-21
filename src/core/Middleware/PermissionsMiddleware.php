<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Entity\Request;
use DB\Entity\RolePermission;
use DB\Entity\User;
use DB\Entity\UserPermission;
use DB\Repository\RolePermissionRepository;
use DB\Repository\UserPermissionRepository;
use DB\Repository\UserRoleRepository;

class PermissionsMiddleware implements MiddlewareInterface
{
    private UserRoleRepository $userRoleRepository;
    private UserPermissionRepository $userPermissionRepository;
    private RolePermissionRepository $rolePermissionRepository;

    public function __construct(
        UserRoleRepository $userRoleRepository,
        UserPermissionRepository $userPermissionRepository,
        RolePermissionRepository $rolePermissionRepository
    ) {
        $this->userRoleRepository = $userRoleRepository;
        $this->userPermissionRepository = $userPermissionRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function handle(Request $request): Request
    {
        /** @var User|null $user */
        $user = $request->getParam('user');
        if ($user === null) {
            $request->addParam('permissions', []);

            return $request;
        }

        $userRoles = $this->userRoleRepository->getByUser($user);
        if (count($userRoles) === 0) {
            $userPermissions = $this->userPermissionRepository->getByUser($user, true);

            $request->addParam('permissions', $this->resolvePermissions([], $userPermissions));

            return $request;
        }

        $rolePermissions = $this->rolePermissionRepository->getByUserRoles($userRoles);
        $userPermissions = $this->userPermissionRepository->getByUser($user);

        $request->addParam('permissions', $this->resolvePermissions($rolePermissions, $userPermissions));

        return $request;
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
