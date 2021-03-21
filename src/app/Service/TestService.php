<?php

declare(strict_types=1);

namespace App\Service;

use Core\Service\Database\MigrationHandler;
use Core\Service\UuidGenerator;
use DB\Entity\User;

class TestService
{
    private MigrationHandler $migrationHandler;

    public function __construct(MigrationHandler $migrationHandler)
    {
        $this->migrationHandler = $migrationHandler;
    }

    public function testMethod(): User
    {
        $this->migrationHandler->handle();

        exit;
    }
}
