<?php

declare(strict_types=1);

namespace App\Service\Permission;

use App\Factory\RoleFactory;
use DB\Entity\Role;
use DB\Repository\RoleRepository;

class RoleManager
{
    private RoleFactory $roleFactory;
    private RoleRepository $roleRepository;
    private RolePermissionManager $rolePermissionManager;

    public function __construct(
        RoleFactory $roleFactory,
        RoleRepository $roleRepository,
        RolePermissionManager $rolePermissionManager
    ) {
        $this->roleFactory = $roleFactory;
        $this->roleRepository = $roleRepository;
        $this->rolePermissionManager = $rolePermissionManager;
    }

    public function create(array $content): Role
    {
        $role = $this->roleFactory->createFromContent($content);
        $role = $this->roleRepository->save($role);

        if (array_key_exists('permissions', $content) && count($content['permissions']) > 0) {
            foreach ($content['permissions'] as $permission) {
                $this->rolePermissionManager->create($role, $permission);
            }
        }

        return $role;
    }
}
