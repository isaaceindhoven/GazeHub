<?php

declare(strict_types=1);

namespace Tests\Services;

use GazeHub\Exceptions\ConfigFileNotExistsException;
use GazeHub\Exceptions\ConfigKeyNotFoundException;
use GazeHub\Services\ConfigRepository;
use PHPUnit\Framework\TestCase;

class ConfigRepositoryTest extends TestCase
{
    public function testShouldAutoloadConfigWhenNoPathIsSupplied()
    {
        // Arrange
        $config = new ConfigRepository();

        // Act
        $config->loadConfig();

        // Assert
        $this->assertNotEmpty($config->get('server_port'));
    }

    public function testShouldLoadCustomConfigFileWhenPathIsSupplied()
    {
        // Arrange
        $config = new ConfigRepository();

        // Act
        $config->loadConfig(__DIR__ . '/../assets/testConfig.php');

        // Assert
        $this->assertEquals('test_value', $config->get('test_key'));
    }

    public function testShouldThrowExceptionWhenConfigFileDoesNotExists()
    {
        // Arrange
        $this->expectException(ConfigFileNotExistsException::class);
        $config = new ConfigRepository();

        // Act
        $config->loadConfig('NON_EXISTING_PATH');

        // Assert
        // Nothing to assert
    }

    public function testShouldThrowExceptionWhenConfigKeyDoesNotExists()
    {
        // Arrange
        $this->expectException(ConfigKeyNotFoundException::class);
        $config = new ConfigRepository();
        $config->loadConfig(__DIR__ . '/../assets/testConfig.php');

        // Act
        $config->get('NON_EXISTING_KEY');

        // Assert
        // Nothing to assert
    }
}
