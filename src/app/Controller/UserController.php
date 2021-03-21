<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Permission;
use App\Normalizer\UserNormalizer;
use App\Service\PermissionTrait;
use App\Service\UserManager;
use Core\Entity\Request;
use Core\Entity\Response;
use Core\Enum\ResponseCode;
use DB\Entity\User;

class UserController
{
    use PermissionTrait;

    private UserNormalizer $normalizer;
    private UserManager $userManager;

    public function __construct(
        UserNormalizer $normalizer,
        UserManager $userManager
    ) {
        $this->normalizer = $normalizer;
        $this->userManager = $userManager;
    }

    public function getSelf(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_SELF_VIEW);

        /** @var User $user */
        $user = $request->getParam('user');

        $normalized = $this->normalizer->normalize($user);

        return $response->withContent($normalized);
    }

    public function updateSelf(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_SELF_UPDATE);

        /** @var User $user */
        $user = $request->getParam('user');
        $data = $request->getContent();

        $updatedUser = $this->userManager->update($user, $data);
        $normalized = $this->normalizer->normalize($updatedUser);

        return $response->withContent($normalized);
    }

    public function get(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_VIEW);

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_CREATE);

        return $response;
    }

    public function update(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_UPDATE);

        return $response;
    }

    public function delete(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_DELETE);

        return $response->withResponseCode(ResponseCode::NO_CONTENT);
    }
}
