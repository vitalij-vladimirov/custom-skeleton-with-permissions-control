<?php

declare(strict_types=1);

namespace App\Controller;

use Core\Entity\Request;
use Core\Entity\Response;

class TestController
{
    public function get(Request $request, Response $response): Response
    {
        return $response;
    }
}
