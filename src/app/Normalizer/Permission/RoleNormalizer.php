<?php

declare(strict_types=1);

namespace App\Normalizer\Permission;

use App\Normalizer\MultipleNormalizerTrait;
use DB\Entity\Role;
use DB\Repository\RolePermissionRepository;

class RoleNormalizer
{
    use MultipleNormalizerTrait;

    private RolePermissionRepository $rolePermissionRepository;
    private PermissionNormalizer $permissionNormalizer;

    public function __construct(
        RolePermissionRepository $rolePermissionRepository,
        PermissionNormalizer $permissionNormalizer
    ) {
        $this->rolePermissionRepository = $rolePermissionRepository;
        $this->permissionNormalizer = $permissionNormalizer;
    }

    public function normalize(Role $role): array
    {
        $rolePermissions = $this->rolePermissionRepository->getByRole($role);
        $permissions = $this->permissionNormalizer->normalizeFromRolePermissions($rolePermissions);

        $normalizedRole = $role->toArray();
        $normalizedRole['permissions'] = $permissions;

        return $normalizedRole;
    }
}
