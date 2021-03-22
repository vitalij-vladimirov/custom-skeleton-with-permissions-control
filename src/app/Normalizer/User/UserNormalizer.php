<?php

declare(strict_types=1);

namespace App\Normalizer\User;

use App\Normalizer\MultipleNormalizerTrait;
use App\Service\User\UserPermissionsResolver;
use DB\Entity\Role;
use DB\Entity\User;
use DB\Repository\RoleRepository;

class UserNormalizer
{
    use MultipleNormalizerTrait;

    private UserPermissionsResolver $userPermissionsResolver;
    private RoleRepository $roleRepository;

    public function __construct(
        UserPermissionsResolver $userPermissionsResolver,
        RoleRepository $roleRepository
    ) {
        $this->userPermissionsResolver = $userPermissionsResolver;
        $this->roleRepository = $roleRepository;
    }

    public function normalize(User $user): array
    {
        $roles = $this->roleRepository->getByUser($user);

        $normalized = $user->toArray();
        $normalized['roles'] = Role::multipleToArray($roles);
        $normalized['permissions'] = $this->userPermissionsResolver->getUserPermissions($user);

        return $normalized;
    }
}
