<?php

declare(strict_types=1);

namespace DB\Repository;

use DB\Entity\Role;
use DB\Entity\User;

class RoleRepository
{
    private UserRoleRepository $userRoleRepository;

    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    /**
     * @param User $user
     *
     * @return Role[]
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

        return $this->getByIds($roleIds);
    }

    /**
     * @param int[] $ids
     *
     * @return Role[]
     */
    public function getByIds(array $ids): array
    {
        $query = sprintf('id IN (%s)', implode(',', $ids));

        return Role::query()
            ->whereQuery($query)
            ->orderBy('id')
            ->get();
    }

    public function getOneByUuid(string $uuid): ?Role
    {
        /** @var Role|null $role */
        $role = Role::query()
            ->whereQuery(sprintf('uuid = \'%s\'', $uuid))
            ->first();

        return $role;
    }

    public function getOneByIdentifier(string $identifier): ?Role
    {
        /** @var Role|null $role */
        $role = Role::query()
            ->whereQuery(sprintf('identifier = \'%s\'', $identifier))
            ->first();

        return $role;
    }
}
