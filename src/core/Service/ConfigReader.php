<?php

declare(strict_types=1);

namespace Core\Service;

class ConfigReader
{
    public function read(string $config = null): array
    {
        if ($config !== null) {
            return $this->readConfigFile($config);
        }
    }

    private function readConfigFile(string $config): array
    {
        $path = sprintf('%s/../../config/%s.php', __DIR__, $config);
        if (!file_exists($path)) {
            return [];
        }

        return require_once $path;
    }
}
