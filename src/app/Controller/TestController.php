<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TestService;
use Core\Entity\Request;
use Core\Entity\Response;

class TestController
{
    private TestService $testService;

    public function __construct(TestService $testService)
    {
        $this->testService = $testService;
    }

    public function get(Request $request, Response $response): Response
    {
        echo $this->testService->testMethod()->format("Y-m-d H:i:s");

        var_dump($request->getParams());
        exit;

        return $response;
    }

    public function post(Request $request, Response $response): Response
    {
        var_dump($request->getParams());
        exit;

        return $response;
    }

    public function put(Request $request, Response $response): Response
    {
        var_dump($request->getParams());
        exit;

        return $response;
    }

    public function patch(Request $request, Response $response): Response
    {
        var_dump($request->getParams());
        exit;

        return $response;
    }

    public function delete(Request $request, Response $response): Response
    {
        var_dump($request->getParams());
        exit;

        return $response;
    }
}
