<?php

declare(strict_types=1);

namespace Core\Service\Database;

use Core\Entity\Database\Migration;
use Core\Service\ConfigReader;
use PDO;

class MigrationHandler
{
    private const MIGRATIONS_DIR = __DIR__ . '/../../../database/Migration/';
    private const SQL_MIME_TYPE = 'text/plain';
    private const SQL_EXT = 'sql';

    private Migration $entity;
    private DB $db;
    private ConfigReader $configReader;
    private PDO $pdo;

    public function __construct(Migration $entity, DB $db, ConfigReader $configReader)
    {
        $this->entity = $entity;
        $this->db = $db;
        $this->configReader = $configReader;
        $this->pdo = $GLOBALS['pdo'];
    }

    public function handle(): void
    {
        if (!$this->checkIfMigrationTableExists()) {
            $this->createMigrationTable();
        }

        $latestVersion = $this->getLatestMigrationVersion();
        $migrations = $this->getListOfMigrations($latestVersion);

        if (count($migrations) === 0) {
            return;
        }

        $this->runMigrations($migrations);
    }

    private function checkIfMigrationTableExists(): bool
    {
        $db = $this->configReader->read('database')['database'];

        $query = sprintf(
            'SELECT * FROM information_schema.tables WHERE table_schema = \'%s\' AND table_name = \'%s\' LIMIT 1',
            $db['database'],
            $this->entity->getTable()
        );

        return count($this->db->rawQuery($query)) > 0;
    }

    private function createMigrationTable(): void
    {
        $query = sprintf('CREATE TABLE IF NOT EXISTS `%s` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `version` INT UNSIGNED NOT NULL,
                `name` VARCHAR(50) NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
                KEY `id` (`id`) USING BTREE
            ) ENGINE=InnoDB COLLATE=utf8mb4_general_ci',
            $this->entity->getTable()
        );

        $this->pdo->exec($query);
    }

    private function getLatestMigrationVersion(): int
    {
        /** @var Migration $migration */
        $migration = Migration::query()
            ->orderBy('version', 'desc')
            ->first();

        if ($migration === null) {
            return 0;
        }

        return $migration->version;
    }

    private function getListOfMigrations(int $latestVersion): array
    {
        $files = scandir(self::MIGRATIONS_DIR, 0);

        $migrations = [];
        foreach ($files as $file) {
            if (
                mime_content_type(self::MIGRATIONS_DIR . $file) !== self::SQL_MIME_TYPE
                || substr($file, -3) !== self::SQL_EXT
            ) {
                continue;
            }

            $version = (int) explode('_', $file)[0];
            if ($version <= $latestVersion) {
                continue;
            }

            $migrations[] = $file;
        }

        return $migrations;
    }

    private function runMigrations(array $migrations): void
    {
        foreach ($migrations as $migration) {
            $query = $this->getQuery($migration);

            $this->pdo->exec($query);

            $migration = substr($migration, 0, -4);
            $splitFilename = explode('_', $migration);

            $entity = new Migration();
            $entity->version = (int) $splitFilename[0];
            $entity->name = implode('_', array_slice($splitFilename, 1));
            $entity->save();
        }
    }

    private function getQuery(string $migration)
    {
        return file_get_contents(self::MIGRATIONS_DIR . $migration);
    }
}
