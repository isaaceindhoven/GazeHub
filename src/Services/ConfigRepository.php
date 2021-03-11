<?php

declare(strict_types=1);

namespace GazeHub\Services;

use GazeHub\Exceptions\ConfigFileNotExistsException;
use GazeHub\Exceptions\ConfigKeyNotFoundException;

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
            throw new ConfigFileNotExistsException(sprintf('No config file found at %s', $path));
        }

        $this->config = include($path);
    }

    public function get(string $key): string
    {
        if (!$this->config) {
            $this->loadConfig();
        }

        if (!array_key_exists($key, $this->config)) {
            throw new ConfigKeyNotFoundException();
        }

        return $this->config[$key];
    }
}
