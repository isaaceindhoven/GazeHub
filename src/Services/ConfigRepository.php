<?php

declare(strict_types=1);

namespace GazeHub\Services;

use GazeHub\Exceptions\ConfigFileNotExists;
use GazeHub\Exceptions\ConfigKeyNotFound;

use function array_key_exists;
use function file_exists;
use function sprintf;

class ConfigRepository
{
    /**
     * @var array
     */
    private $config;

    public function loadConfig(string $path = null): void
    {
        if ($path === null) {
            $path = __DIR__ . '/../../config/config.php';
        }

        if (!file_exists($path)) {
            throw new ConfigFileNotExists(sprintf('No config file found at %s', $path));
        }

        $this->config = include($path);
    }

    public function get(string $key): mixed
    {
        if (!$this->config) {
            $this->loadConfig();
        }

        if (!array_key_exists($key, $this->config)) {
            throw new ConfigKeyNotFound();
        }

        return $this->config[$key];
    }
}
