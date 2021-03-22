<?php

namespace App\Route;

use App\Controller\RoleController;
use Core\Entity\Route;
use Core\Enum\HttpMethod;

return [
    '/api' => [
        '/role' => [
            HttpMethod::GET => Route::create(RoleController::class, 'get', true),
            HttpMethod::POST => Route::create(RoleController::class, 'create', true),
            HttpMethod::DELETE => Route::create(RoleController::class, 'delete', true),
        ],
    ],
];