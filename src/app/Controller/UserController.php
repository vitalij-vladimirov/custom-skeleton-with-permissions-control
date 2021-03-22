<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Permission;
use App\Normalizer\User\SelfNormalizer;
use App\Normalizer\User\UserNormalizer;
use App\Service\PermissionTrait;
use App\Service\User\UserManager;
use Core\Entity\Request;
use Core\Entity\Response;
use Core\Enum\ResponseCode;
use Core\Exception\Api\NotFoundException;
use DB\Entity\User;
use DB\Repository\UserRepository;

class UserController
{
    use PermissionTrait;

    private UserManager $userManager;
    private UserRepository $userRepository;
    private SelfNormalizer $selfNormalizer;
    private UserNormalizer $userNormalizer;

    public function __construct(
        UserManager $userManager,
        UserRepository $userRepository,
        SelfNormalizer $selfNormalizer,
        UserNormalizer $userNormalizer
    ) {
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
        $this->selfNormalizer = $selfNormalizer;
        $this->userNormalizer = $userNormalizer;
    }

    public function getSelf(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_SELF_VIEW);

        /** @var User $user */
        $user = $request->getParam('user');

        $normalized = $this->selfNormalizer->normalize($user);

        return $response->withContent($normalized);
    }

    public function updateSelf(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_SELF_UPDATE);

        /** @var User $user */
        $user = $request->getParam('user');
        $data = $request->getContent();

        $updatedUser = $this->userManager->update($user, $data);
        $normalized = $this->selfNormalizer->normalize($updatedUser);

        return $response->withContent($normalized);
    }

    public function get(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_VIEW);
        $this->isNotSelfModify($request);

        /** @var User $self */
        $self = $request->getParam('user');
        $identifier = $request->getParam('identifier');

        if ($identifier === null) {
            $users = $this->userRepository->getAllExceptSelf($self);

            $normalized = $this->userNormalizer->normalizeMany($users);

            return $response->withContent($normalized);
        }

        $user = $this->userRepository->getOneByUuid($identifier);
        if ($user === null) {
            throw new NotFoundException();
        }

        $normalized = $this->userNormalizer->normalize($user);

        return $response->withContent($normalized);
    }

    public function create(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_CREATE);

        $content = $request->getContent();

        $user = $this->userManager->create($content);
        $normalized = $this->userNormalizer->normalize($user);

        return $response
            ->withContent($normalized)
            ->withResponseCode(ResponseCode::CREATED);
    }

    public function update(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_UPDATE);
        $this->isNotSelfModify($request);

        $identifier = $request->getParam('identifier');
        if ($identifier === null) {
            throw new NotFoundException();
        }

        $user = $this->userRepository->getOneByUuid($identifier);
        if ($user === null) {
            throw new NotFoundException();
        }

        $data = $request->getContent();

        $updatedUser = $this->userManager->update($user, $data);
        $normalized = $this->selfNormalizer->normalize($updatedUser);

        return $response->withContent($normalized);
    }

    public function delete(Request $request, Response $response): Response
    {
        $this->hasPermission($request, Permission::USER_DELETE);
        $this->isNotSelfModify($request);

        $identifier = $request->getParam('identifier');
        if ($identifier === null) {
            throw new NotFoundException();
        }

        $user = $this->userRepository->getOneByUuid($identifier);
        if ($user === null) {
            throw new NotFoundException();
        }

        $this->userRepository->delete($user);

        return $response->withResponseCode(ResponseCode::NO_CONTENT);
    }
}
