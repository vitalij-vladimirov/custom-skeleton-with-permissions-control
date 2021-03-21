<?php

declare(strict_types=1);

namespace Core;

use Core\Entity\Route;
use Core\Exception\Api\InternalServerException;
use Core\Entity\Request;
use Core\Entity\Response;
use Core\Enum\Environment;
use Core\Enum\HttpMethod;
use Core\Middleware\AuthenticationMiddleware;
use Core\Middleware\MiddlewareInterface;
use Core\Middleware\PermissionsMiddleware;
use Core\Service\ConfigReader;
use Core\Service\DependencyInjector;
use Core\Service\ExceptionHandler;
use Core\Service\RoutesHandler;
use Throwable;
use PDO;
use ReflectionMethod;
use ReflectionNamedType;

class Bootstrap
{
    public DependencyInjector $di;
    public PDO $pdo;

    private Route $route;
    private Request $request;
    private Response $response;

    /**
     * @var MiddlewareInterface[]
     *
     * Setup a list of middleware here
     */
    private array $middleware = [
        AuthenticationMiddleware::class,
        PermissionsMiddleware::class,
    ];

    public function __construct()
    {
        $this->loadApp();
    }

    public function runApi(): void
    {
        try {
            $this->setRoute();
            $this->validateController();
            $this->setupRequest();
            $this->runMiddleware();
            $this->runController();
            $this->respondSuccess();
        } catch (Throwable $exception) {
            (new ExceptionHandler())->handle($exception);
        }
    }

    private function loadApp(): self
    {
        try {
            $this->setDefaults();
            $this->setupDI();
            $this->setupDatabaseConnection();
        } catch (Throwable $exception) {
            (new ExceptionHandler())->handle($exception);
        }

        return $this;
    }

    private function setDefaults(): void
    {
        $appConfig = (new ConfigReader())->read('app');

        define('ENVIRONMENT', $appConfig['app']['environment'] ?? Environment::PRODUCTION);
    }

    private function setupDI(): void
    {
        $this->di = new DependencyInjector();
    }

    private function setupDatabaseConnection(): void
    {
        /** @var ConfigReader $config */
        $config = $this->di->inject(ConfigReader::class);
        $db = $config->read('database')['database'];

        $this->pdo = new PDO(
            sprintf('mysql:host=%s:%d;dbname=%s', $db['host'], $db['port'], $db['database']),
            $db['username'],
            $db['password']
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function setRoute(): void
    {
        /** @var RoutesHandler $routesReader */
        $routesReader = $this->di->inject(RoutesHandler::class);
        $this->route = $routesReader->getRoute();
    }

    private function validateController(): void
    {
        $controller = $this->route->class;
        $method = $this->route->method;

        $reflection = (new ReflectionMethod($controller, $method));

        if ($reflection->getNumberOfParameters() !== 2) {
            throw new InternalServerException();
        }

        $parameters = $reflection->getParameters();

        $firstParameter = $parameters[0]->getClass();
        $firstParameterName = $firstParameter !== null ? $firstParameter->getName() : null;

        $secondParameter = $parameters[1]->getClass();
        $secondParameterName = $secondParameter !== null ? $secondParameter->getName() : null;

        /** @var ReflectionNamedType $returnType */
        $returnType = $reflection->getReturnType();
        $returnName = $returnType !== null ? $returnType->getName() : null;

        if (
            $firstParameterName !== Request::class
            || $secondParameterName !== Response::class
            || $returnName !== Response::class
        ) {
            throw new InternalServerException();
        }
    }

    private function setupRequest(): void
    {
        $headers = getallheaders();
        $httpMethod = strtolower($_SERVER['REQUEST_METHOD']);

        switch ($httpMethod) {
            case HttpMethod::GET:
                $content = $_GET;
                break;
            case HttpMethod::POST:
            case HttpMethod::PUT:
            case HttpMethod::PATCH:
                $body = file_get_contents('php://input');
                $content = $body !== '' ? json_decode($body, true) : [];
                break;
            default:
                $content = [];
        }

        if ($this->route->identifier !== null) {
            $content['identifier'] = $this->route->identifier;
        }

        $this->request = new Request($content, $headers);
    }

    private function runMiddleware(): void
    {
        if (count($this->middleware) === 0) {
            return;
        }

        foreach ($this->middleware as $middleware) {
            /** @var MiddlewareInterface $middlewareClass */
            $middlewareClass = $this->di->inject($middleware);

            if (!$middlewareClass instanceof MiddlewareInterface) {
                throw new InternalServerException();
            }

            $this->request = $middlewareClass->handle($this->request);
        }
    }

    private function runController(): void
    {
        $controller = $this->di->inject($this->route->class);
        $this->response = $controller->{$this->route->method}($this->request, new Response());
    }

    private function respondSuccess(): void
    {
        header('Content-Type: text/plain charset=UTF-8');
        header('Content-type: application/json');
        http_response_code($this->response->getResponseCode());

        $headers = $this->response->getHeaders();
        if (count($headers) !== 0) {
            foreach ($headers as $headerName => $headerValue) {
                header(sprintf('%s: %s', $headerName, $headerValue));
            }
        }

        $content = $this->response->getContent();
        if (count($content) !== 0) {
            echo json_encode($content, JSON_THROW_ON_ERROR);
        }
    }
}
