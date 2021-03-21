<?php

declare(strict_types=1);

namespace Core;

use Core\Exception\Api\InternalServerException;
use Core\Exception\Api\NotFoundException;
use Core\Entity\Request;
use Core\Entity\Response;
use Core\Enum\Environment;
use Core\Enum\HttpMethod;
use Core\Enum\ResponseCode;
use Core\Middleware\AuthenticationMiddleware;
use Core\Middleware\MiddlewareInterface;
use Core\Middleware\PermissionsMiddleware;
use Core\Service\ConfigReader;
use Core\Service\DependencyInjector;
use Core\Service\ExceptionHandler;
use Throwable;
use PDO;
use ReflectionMethod;
use ReflectionNamedType;

class Bootstrap
{
    public DependencyInjector $di;
    public PDO $pdo;

    private string $controller;
    private string $method;
    private ?string $identifier = null;
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
            $this->setControllerClass();
            $this->setControllerMethod();
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

    private function setControllerClass(): void
    {
        $path = explode('/', strtok($_SERVER["REQUEST_URI"], '?'));
        $path = array_slice($path, 1);
        foreach ($path as $key => $value) {
            $value = str_replace(['-', '_'], ' ', $value);
            $value = ucwords($value);
            $camelcasePath[$key] = str_replace(' ', '', $value);
        }

        $controllerPath = sprintf('../app/Controller/%sController.php', implode('/', $camelcasePath));
        if (!file_exists($controllerPath) && HttpMethod::isIdentifierAllowed() && count($camelcasePath) >= 2) {
            $lastPathKey = array_key_last($camelcasePath);
            $this->identifier = $path[$lastPathKey];
            unset($camelcasePath[$lastPathKey]);

            $controllerPath = sprintf('../app/Controller/%sController.php', implode('/', $camelcasePath));
        }

        if (!file_exists($controllerPath)) {
            throw new NotFoundException();
        }

        require_once $controllerPath;

        $this->controller = sprintf('\App\Controller\%sController', implode('\\', $camelcasePath));
    }

    private function setControllerMethod(): void
    {
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($this->method === HttpMethod::OPTIONS) {
            $this->respondWithOptions();
        }

        if (!method_exists($this->controller, $this->method)) {
            throw new NotFoundException();
        }
    }

    private function validateController(): void
    {
        $reflection = (new ReflectionMethod($this->controller, $this->method));

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

        switch ($this->method) {
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
                $content = '';
        }

        if ($this->identifier !== null) {
            $content['identifier'] = $this->identifier;
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
        $controller = $this->di->inject($this->controller);
        $this->response = $controller->{$this->method}($this->request, new Response());
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

    private function respondWithOptions(): void
    {
        $methods = [];
        foreach (HttpMethod::getAllowMethods() as $allowMethod) {
            if (method_exists($this->controller, $allowMethod)) {
                $methods[] = strtoupper($allowMethod);
            }
        }

        header('Content-Type: text/plain charset=UTF-8');
        header('Access-Control-Allow-Methods: ' . implode(',', $methods));
        http_response_code(ResponseCode::NO_CONTENT);

        exit;
    }
}
