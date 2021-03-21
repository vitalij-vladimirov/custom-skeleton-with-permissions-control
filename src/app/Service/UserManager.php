<?php

declare(strict_types=1);

namespace App\Service;

use DB\Entity\User;

class UserManager
{
    private PasswordManager $passwordManager;

    public function __construct(PasswordManager $passwordManager)
    {
        $this->passwordManager = $passwordManager;
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
}
