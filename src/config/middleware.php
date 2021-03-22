<?php

namespace App\Config;

return [
    \App\Middleware\AuthenticationMiddleware::class,
    \App\Middleware\PermissionsMiddleware::class,
];
