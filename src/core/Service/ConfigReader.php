<?php

declare(strict_types=1);

namespace Core\Service;

class ConfigReader
{
    public function read(string $configFile = null): array
    {
        if ($configFile !== null) {
            return [$configFile => $this->readConfigFile($configFile)];
        }

        return $this->readConfigDirectory();
    }

    private function readConfigDirectory(): array
    {
        $dir = sprintf('%s/../../config/', __DIR__);
        $files = scandir($dir);

        $config = [];
        foreach ($files as $file) {
            if (mime_content_type($dir . $file) !== 'text/x-php') {
                continue;
            }

            $configFile = substr($file, 0, -4);
            $readConfig = $this->readConfigFile($configFile);
            if (count($readConfig) === 0) {
                continue;
            }

            $config[$configFile] = $readConfig;
        }

        return $config;
    }

    private function readConfigFile(string $configFile): array
    {
        $path = sprintf('%s/../../config/%s.php', __DIR__, $configFile);
        if (!file_exists($path)) {
            return [];
        }

        return require_once $path;
    }
}
