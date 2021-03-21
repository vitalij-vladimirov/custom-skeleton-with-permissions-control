<?php

declare(strict_types=1);

namespace Core\Service\Database;

use Core\Entity\Database\BaseDbEntity;
use Core\Enum\Environment;
use Core\Service\ConfigReader;
use Core\Service\DependencyInjector;

class SeedHandler
{
    private DependencyInjector $di;
    private ConfigReader $configReader;

    public function __construct(
        DependencyInjector $di,
        ConfigReader $configReader
    ) {
        $this->di = $di;
        $this->configReader = $configReader;
    }

    public function handle(): void
    {
        if (ENVIRONMENT !== Environment::LOCAL) {
            return;
        }
        
        $seeds = $this->configReader->read('seed')['seed'];
        foreach ($seeds as $seedClass => $affectedTables) {
            // Seed Class is ran only if all affected tables are empty
            if (!$this->areTablesEmpty($affectedTables)) {
                continue;
            }

            /** @var BaseSeed $seed */
            $seed = $this->di->inject($seedClass);
            $seed->run();
        }
    }

    private function areTablesEmpty(array $tables): bool
    {
        if (count($tables) === 0) {
            return true;
        }

        foreach ($tables as $table) {
            /** @var BaseDbEntity $entity */
            $entity = new $table();

            if ($entity::query()->exists()) {
                return false;
            }
        }

        return true;
    }
}
