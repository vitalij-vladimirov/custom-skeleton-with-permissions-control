<?php

declare(strict_types=1);

namespace Core\Service;

class ConfigReader
{
    private const CONFIG_DIR = '%s/../../config/';
    private const PHP_MIME_TYPE = 'text/x-php';
    private const PHP_EXT = 'php';

    public function read(string $configFile = null): array
    {
        if ($configFile !== null) {
            return [$configFile => $this->readConfigFile($configFile)];
        }

        return $this->readConfigDirectory();
    }

    private function readConfigDirectory(): array
    {
        $dir = sprintf(self::CONFIG_DIR, __DIR__);
        $files = scandir($dir);

        $config = [];
        foreach ($files as $file) {
            if (mime_content_type($dir . $file) !== self::PHP_MIME_TYPE) {
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
        $path = sprintf(self::CONFIG_DIR . $configFile . '.' . self::PHP_EXT, __DIR__);

        if (!file_exists($path)) {
            return [];
        }

        return require $path;
    }
}
