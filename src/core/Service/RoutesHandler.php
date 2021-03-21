<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Entity\Route;
use Core\Enum\HttpMethod;
use Core\Exception\Api\NotFoundException;

class RoutesHandler
{
    private const ROUTES_DIR = __DIR__ . '/../../app/Route/';
    private const PHP_MIME_TYPE = 'text/x-php';

    public function getRoute(): Route
    {
        $routes = $this->collectRoutes();

        return $this->resolveRoute($routes);
    }

    private function collectRoutes(): array
    {
        $files = scandir(self::ROUTES_DIR);

        $routes = [];
        foreach ($files as $file) {
            if (mime_content_type(self::ROUTES_DIR . $file) !== self::PHP_MIME_TYPE) {
                continue;
            }

            $route = require self::ROUTES_DIR . $file;
            $routes = $this->appendRoutes($routes, $route);
        }

        return $routes;
    }

    private function appendRoutes(array $routes, array $route): array
    {
        foreach ($route as $key => $value) {
            // If new route key does not exists in already created routes array
            if (!array_key_exists($key, $routes)) {
                $routes[$key] = $value;

                continue;
            }

            // If new route key exists in routes and they are both array
            if (is_array($routes[$key]) && is_array($value)) {
                $routes[$key] = $this->appendRoutes($routes[$key], $value);
            }
        }

        return $routes;
    }

    private function resolveRoute(array $routes): Route
    {
        $method = $this->getMethod();

        $path = explode('/', strtok($_SERVER["REQUEST_URI"], '?'));
        $path = array_slice($path, 1);

        $route = $this->readRoutes($routes, $path, $method);
        if (!method_exists($route->class, $route->method)) {
            throw new NotFoundException();
        }

        return $route;
    }

    private function getMethod(): string
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (!in_array($method, HttpMethod::getAllowMethods(), true)) {
            throw new NotFoundException();
        }

        return $method;
    }

    private function readRoutes(array $routes, array $path, string $method): Route
    {
        if (
            count($path) === 1
            && !array_key_exists('/' . $path[0], $routes)
            && array_key_exists($method, $routes)
            && $routes[$method] instanceof Route
            && $routes[$method]->hasIdentifier
        ) {
            return $routes[$method]->withIdentifier($path[0]);
        }

        if (array_key_exists($method, $routes) && $routes[$method] instanceof Route && count($path) === 0) {
            return $routes[$method];
        }

        if (
            array_key_exists('/', $routes)
            && array_key_exists($method, $routes['/'])
            && $routes['/'][$method] instanceof Route
            && count($path) === 0
        ) {
            return $routes['/'][$method];
        }

        if (!array_key_exists('/' . $path[0], $routes)) {
            throw new NotFoundException();
        }

        return $this->readRoutes($routes['/' . $path[0]], array_slice($path, 1), $method);
    }
}
