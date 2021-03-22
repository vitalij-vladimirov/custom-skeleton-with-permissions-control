<?php

declare(strict_types=1);

namespace App\Service;

use Core\Entity\Request;
use Core\Exception\Api\BadRequestException;
use Core\Exception\Api\ForbiddenException;
use DB\Entity\User;

trait PermissionTrait
{
    public function hasPermission(Request $request, string $permission): void
    {
        $userPermissions = $request->getParam('permissions');

        if (!in_array($permission, $userPermissions, true)) {
            throw new ForbiddenException();
        }
    }

    public function isNotSelfModify(Request $request): void
    {
        $identifier = $request->getParam('identifier');
        if ($identifier === null) {
            return;
        }

        /** @var User $user */
        $user = $request->getParam('user');
        if ($user->uuid === $identifier) {
            throw new BadRequestException();
        }
    }
}
