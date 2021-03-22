<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Permission;
use App\Normalizer\Permission\RoleNormalizer;
use App\Service\Permission\RoleManager;
use App\Service\PermissionTrait;
use Core\Entity\Request;
use Core\Entity\Response;
use Core\Enum\ResponseCode;
use Core\Exception\Api\NotFoundException;
use DB\Repository\RoleRepository;

class RoleController
{
    use PermissionTrait;

    private RoleManager $roleManager;
    private RoleRepository $roleRepository;
    private RoleNormalizer $roleNormalizer;

    public function __construct(
        RoleManager $roleManager,
        RoleRepository $roleRepository,
        RoleNormalizer $roleNormalizer
    ) {
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
        $this->roleNormalizer = $roleNormalizer;
    }

    public function get(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::ROLE_VIEW);

        $identifier = $request->getParam('identifier');

        if ($identifier === null) {
            $roles = $this->roleRepository->get();

            $normalized = $this->roleNormalizer->normalizeMany($roles);

            return $response->withContent($normalized);
        }

        $role = $this->roleRepository->getOneByUuid($identifier);
        if ($role === null) {
            throw new NotFoundException();
        }

        $normalized = $this->roleNormalizer->normalize($role);

        return $response->withContent($normalized);
    }

    public function create(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_CREATE);

        $content = $request->getContent();

        $role = $this->roleManager->create($content);
        $normalized = $this->roleNormalizer->normalize($role);

        return $response
            ->withContent($normalized)
            ->withResponseCode(ResponseCode::CREATED);
    }

    public function delete(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_DELETE);

        $identifier = $request->getParam('identifier');
        if ($identifier === null) {
            throw new NotFoundException();
        }

        $role = $this->roleRepository->getOneByUuid($identifier);
        if ($role === null) {
            throw new NotFoundException();
        }

        $this->roleRepository->delete($role);

        return $response->withResponseCode(ResponseCode::NO_CONTENT);
    }
}
