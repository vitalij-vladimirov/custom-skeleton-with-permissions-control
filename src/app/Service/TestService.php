<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;

class TestService
{
    public function testMethod(): DateTime
    {
        return new DateTime();
    }
}
