<?php

declare(strict_types=1);

namespace DB\Seed;

use Core\Service\Database\BaseSeed;
use Core\Service\PasswordManager;
use DB\Entity\Role;
use DB\Entity\User;

class UserAndRoleSeed implements BaseSeed
{
    private const PASSWORD = 'Str0ngPassw0rd';

    private PasswordManager $passwordManager;

    public function __construct(PasswordManager $passwordManager)
    {
        $this->passwordManager = $passwordManager;
    }

    public function run(): void
    {
        $users = $this->createUsers();
        $roles = $this->createRoles();
    }

    /**
     * @return User[]
     */
    private function createUsers(): array
    {
        $users = [];

        $user = new User();
        $user->firstName = 'Jim';
        $user->lastName = 'Beam';
        $user->email = 'jim@beam.com';
        $user->password = $this->passwordManager->hashPassword(self::PASSWORD);
        $users[] = $user->save();

        $user = new User();
        $user->firstName = 'Jack';
        $user->lastName = 'Daniels';
        $user->email = 'jack@daniels.com';
        $user->password = $this->passwordManager->hashPassword(self::PASSWORD);
        $users[] = $user->save();

        $user = new User();
        $user->firstName = 'Captain';
        $user->lastName = 'Morgan';
        $user->email = 'captains@morgan.com';
        $user->password = $this->passwordManager->hashPassword(self::PASSWORD);
        $users[] = $user->save();

        return $users;
    }

    /**
     * @return Role[]
     */
    private function createRoles(): array
    {
        $roles = [];

        $role = new Role();
        $role->identifier = 'sysadmin';
        $role->title = 'System administrator';
        $roles[] = $role->save();

        $role = new Role();
        $role->identifier = 'admin';
        $role->title = 'Administrator';
        $roles[] = $role->save();

        $role = new Role();
        $role->identifier = 'user';
        $role->title = 'User';
        $roles[] = $role->save();

        return $roles;
    }
}
