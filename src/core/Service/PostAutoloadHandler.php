<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Bootstrap;
use Core\Service\Database\MigrationHandler;
use Core\Service\Database\SeedHandler;
use Throwable;

class PostAutoloadHandler
{
    public static function postAutoloadDump(): void
    {
        $app = new Bootstrap();
        $GLOBALS['pdo'] = $app->pdo;

        /** @var MigrationHandler $migrationsHandler */
        $migrationsHandler = $app->di->inject(MigrationHandler::class);
        try {
            $migrationsHandler->handle();

            echo "\e[32mDatabase migrated successfully.\e[0m\n";
        } catch (Throwable $throwable) {
            echo "\e[31mDatabase migration failed.\e[0m\n";
        }

        /** @var SeedHandler $seedHandler */
        $seedHandler = $app->di->inject(SeedHandler::class);
        try {
            $seedHandler->handle();

            echo "\e[32mDatabase seeded successfully.\e[0m\n";
        } catch (Throwable $throwable) {
            echo "\e[31mDatabase seeding failed.\e[0m\n";
        }
    }
}
