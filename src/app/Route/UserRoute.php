<?php

namespace App\Route;

use App\Controller\UserController;
use Core\Entity\Route;
use Core\Enum\HttpMethod;

return [
    '/api' => [
        '/user' => [
            '/self' => [
                HttpMethod::GET => Route::create(UserController::class, 'getSelf'),
                HttpMethod::PATCH => Route::create(UserController::class, 'updateSelf'),
            ],
            HttpMethod::GET => Route::create(UserController::class, 'get', true),
            HttpMethod::POST => Route::create(UserController::class, 'create'),
            HttpMethod::PATCH => Route::create(UserController::class, 'update', true),
            HttpMethod::DELETE => Route::create(UserController::class, 'delete', true),
        ],
    ],
];