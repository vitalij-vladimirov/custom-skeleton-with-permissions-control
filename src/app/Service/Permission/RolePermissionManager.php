<?php

declare(strict_types=1);

namespace App\Service\Permission;

use App\Factory\RolePermissionFactory;
use DB\Entity\Role;
use DB\Entity\RolePermission;
use DB\Repository\RolePermissionRepository;

class RolePermissionManager
{
    private RolePermissionFactory $rolePermissionFactory;
    private RolePermissionRepository $rolePermissionRepository;

    public function __construct(
        RolePermissionFactory $rolePermissionFactory,
        RolePermissionRepository $rolePermissionRepository
    ) {
        $this->rolePermissionFactory = $rolePermissionFactory;
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function create(Role $role, $permission): RolePermission
    {
        $permission = $this->rolePermissionFactory->create($role, $permission);

        return $this->rolePermissionRepository->save($permission);
    }
}
