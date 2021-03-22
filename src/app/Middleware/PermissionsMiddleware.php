<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\User\UserPermissionsResolver;
use Core\Entity\Request;
use Core\Middleware\MiddlewareInterface;
use DB\Entity\User;

class PermissionsMiddleware implements MiddlewareInterface
{
    private UserPermissionsResolver $userPermissionsResolver;

    public function __construct(UserPermissionsResolver $userPermissionsResolver)
    {
        $this->userPermissionsResolver = $userPermissionsResolver;
    }

    public function handle(Request $request): Request
    {
        /** @var User|null $user */
        $user = $request->getParam('user');
        if ($user === null) {
            $request->addParam('permissions', []);

            return $request;
        }

        $userPermissions = $this->userPermissionsResolver->getUserPermissions($user);

        return $request->addParam('permissions', $userPermissions);
    }
}
