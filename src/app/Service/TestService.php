<?php

declare(strict_types=1);

namespace App\Service;

use Core\Service\UuidGenerator;
use DB\Entity\User;

class TestService
{
    private UuidGenerator $uuidGenerator;

    public function __construct(UuidGenerator $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    public function testMethod(): User
    {
        /** @var User $user */
        $user = User::query()
            ->whereQuery('id = 5')
            ->first();

//        $user = new User();
//        $user->id = 1;
//        $user->uuid = 'test123';
//        $user->email = 'jack@daniels.com';
//        $user->firstName = 'Jim';
//        $user->lastName = 'Beam';
//        $user->password = 'abc';

//        var_dump($user);
        var_dump($user->delete());

//        var_dump();
        exit;
    }
}
