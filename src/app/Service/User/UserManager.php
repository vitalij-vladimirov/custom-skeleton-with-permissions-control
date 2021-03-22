<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Factory\UserFactory;
use App\Service\PasswordManager;
use App\Service\Permission\UserPermissionManager;
use App\Service\Permission\UserRoleManager;
use Core\Exception\Api\BadRequestException;
use DB\Entity\User;

class UserManager
{
    private PasswordManager $passwordManager;
    private UserFactory $userFactory;
    private UserRoleManager $userRoleManager;
    private UserPermissionManager $userPermissionManager;

    public function __construct(
        PasswordManager $passwordManager,
        UserFactory $userFactory,
        UserRoleManager $userRoleManager,
        UserPermissionManager $userPermissionManager
    ) {
        $this->passwordManager = $passwordManager;
        $this->userFactory = $userFactory;
        $this->userRoleManager = $userRoleManager;
        $this->userPermissionManager = $userPermissionManager;
    }

    public function update(User $user, array $data): User
    {
        if (array_key_exists('email', $data) && $user->email !== $data['email']) {
            $user->email = $data['email'];
        }

        if (array_key_exists('first_name', $data) && $user->firstName !== $data['first_name']) {
            $user->firstName = $data['first_name'];
        }

        if (array_key_exists('last_name', $data) && $user->lastName !== $data['last_name']) {
            $user->lastName = $data['last_name'];
        }

        if (
            array_key_exists('password', $data)
            && !$this->passwordManager->verifyPassword($data['password'], $user->password)
        ) {
            $user->password = $this->passwordManager->hashPassword($data['password']);
        }

        return $user->save();
    }

    public function create(array $content): User
    {
        $user = $this->userFactory->createFromContent($content)->save();

        if ($user === null) {
            throw new BadRequestException();
        }

        if (array_key_exists('roles', $content) && count($content['roles']) > 0) {
            foreach ($content['roles'] as $item) {
                $this->userRoleManager->create($user, $item);
            }
        }

        if (array_key_exists('permissions', $content) && count($content['permissions']) > 0) {
            foreach ($content['permissions'] as $item) {
                $this->userPermissionManager->create($user, $item['permission'], $item['grant']);
            }
        }

        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
