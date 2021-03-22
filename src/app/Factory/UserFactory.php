<?php

declare(strict_types=1);

namespace App\Factory;

use App\Service\PasswordManager;
use DB\Entity\User;

class UserFactory
{
    private PasswordManager $passwordManager;

    public function __construct(PasswordManager $passwordManager)
    {
        $this->passwordManager = $passwordManager;
    }

    public function create(string $firstName, string $lastName, string $email, string $password): User
    {
        $user = new User();
        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->email = $email;
        $user->password = $this->passwordManager->hashPassword($password);

        return $user;
    }

    public function createFromContent(array $content): User
    {
        $user = new User();
        $user->firstName = $content['first_name'];
        $user->lastName = $content['last_name'];
        $user->email = $content['email'];
        $user->password = $this->passwordManager->hashPassword($content['password']);

        return $user;
    }
}
