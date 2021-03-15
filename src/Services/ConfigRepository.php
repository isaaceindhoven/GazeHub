<?php

/**
  *   Do not remove or alter the notices in this preamble.
  *   This software code regards ISAAC Standard Software.
  *   Copyright © 2021 ISAAC and/or its affiliates.
  *   www.isaac.nl All rights reserved. License grant and user rights and obligations
  *   according to applicable license agreement. Please contact sales@isaac.nl for
  *   questions regarding license and user rights.
  */

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

    /**
     * Load configuration file in memory, if path is null, /config/config.php is loaded
     *
     * @param string|null       $path           Path to config file to load
     * @throws ConfigFileNotExistsException     Thrown when the config file does not exist
     */
    public function loadConfig(string $path = null)
    {
        if ($path === null) {
            $path = __DIR__ . '/../../config/config.php';
        }

        if (!file_exists($path)) {
            throw new ConfigFileNotExistsException(sprintf('No config file found at %s', $path));
        }

        $this->config = include($path);
    }

    /**
     * Get a value from the loaded configuration
     *
     * @param string        $key            Key to load value for
     * @return mixed                        Value from configuration
     * @throws ConfigFileNotExistsException Thrown when the config file does not exist
     * @throws ConfigKeyNotFoundException   Thrown when key not found in config file
     */
    public function get(string $key)
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
