<?php

declare(strict_types=1);

namespace App\Service;

use Core\Service\Database\MigrationHandler;
use Core\Service\Database\SeedHandler;
use DB\Entity\User;

class TestService
{
    private SeedHandler $seedHandler;
    private MigrationHandler $migrationHandler;

    public function __construct(SeedHandler $seedHandler, MigrationHandler $migrationHandler)
    {
        $this->seedHandler = $seedHandler;
        $this->migrationHandler = $migrationHandler;
    }

    public function testMethod(): User
    {
        $this->seedHandler->handle();

        exit;
    }
}
