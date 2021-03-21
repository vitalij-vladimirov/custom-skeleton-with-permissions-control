<?php

declare(strict_types=1);

namespace Core\Middleware;

use App\Service\PasswordManager;
use Core\Entity\Request;
use Core\Exception\Api\UnauthorizedException;
use DB\Repository\UserRepository;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private UserRepository $userRepository;
    private PasswordManager $passwordManager;

    public function __construct(UserRepository $userRepository, PasswordManager $passwordManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordManager = $passwordManager;
    }

    public function handle(Request $request): Request
    {
        $auth = $request->getHeader('Authorization');

        if ($auth === null || strpos($auth, 'Basic ') === false) {
            throw new UnauthorizedException();
        }

        [$email, $password] = explode(':', base64_decode(substr($auth, 6)));

        $user = $this->userRepository->getOneByEmail($email);
        if ($user === null || !$this->passwordManager->verifyPassword($password, $user->password)) {
            throw new UnauthorizedException();
        }

        $request->addParam('user', $user);

        return $request;
    }
}
