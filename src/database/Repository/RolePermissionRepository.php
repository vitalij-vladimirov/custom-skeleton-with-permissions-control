<?php

declare(strict_types=1);

namespace DB\Repository;

use DB\Entity\Role;
use DB\Entity\RolePermission;
use DB\Entity\User;

class RolePermissionRepository
{
    private UserRoleRepository $userRoleRepository;

    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @param User $user
     *
     * @return RolePermission[]
     */
    public function getByUser(User $user): array
    {
        $userRoles = $this->userRoleRepository->getByUser($user);
        if (count($userRoles) === 0) {
            return [];
        }

        $roleIds = [];
        foreach ($userRoles as $role) {
            $roleIds[] = $role->roleId;
        }

        return $this->getByRoleIds($roleIds);
    }

    /**
     * @param int[] $roleIds
     *
     * @return RolePermission[]
     */
    private function getByRoleIds(array $roleIds): array
    {
        $query = sprintf('role_id IN (%s)', implode(',', $roleIds));

        return RolePermission::query()
            ->whereQuery($query)
            ->orderBy('id')
            ->get();
    }

    public function getByRole(Role $role): array
    {
        $query = sprintf('role_id = %d', $role->id);

        return RolePermission::query()
            ->whereQuery($query)
            ->get();
    }

    public function save(RolePermission $rolePermission): RolePermission
    {
        return $rolePermission->save();
    }

    public function delete(RolePermission $rolePermission): bool
    {
        return $rolePermission->delete();
    }
}
