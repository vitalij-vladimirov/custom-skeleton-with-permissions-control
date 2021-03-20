<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\Entity\Request;

interface MiddlewareInterface
{
    public function handle(Request $request): Request;
}
