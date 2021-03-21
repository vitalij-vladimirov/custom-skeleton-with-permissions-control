<?php

declare(strict_types=1);

namespace App\Service;

use Core\Entity\Request;
use Core\Exception\Api\ForbiddenException;

trait PermissionTrait
{
    public function hasPermission(Request $request, string $permission): void
    {
        $userPermissions = $request->getParam('permissions');

        if (!in_array($permission, $userPermissions, true)) {
            throw new ForbiddenException();
        }
    }
}
