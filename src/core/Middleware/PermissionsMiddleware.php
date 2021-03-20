<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Entity\Request;

class PermissionsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request): Request
    {
        return $request;
    }
}
