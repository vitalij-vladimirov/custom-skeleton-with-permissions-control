<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Enum\Permission;
use App\Normalizer\UserNormalizer;
use App\Service\PermissionTrait;
use App\Service\UserManager;
use Core\Entity\Request;
use Core\Entity\Response;
use DB\Entity\User;

class SelfController
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

    public function get(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_SELF_VIEW);

        /** @var User $user */
        $user = $request->getParam('user');

        $normalized = $this->normalizer->normalize($user);

        return $response->withContent($normalized);
    }

    public function patch(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_SELF_UPDATE);

        /** @var User $user */
        $user = $request->getParam('user');
        $data = $request->getContent();

        $updatedUser = $this->userManager->update($user, $data);
        $normalized = $this->normalizer->normalize($updatedUser);

        return $response->withContent($normalized);
    }
}
