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

namespace Tests;

use DI\Container;
use GazeHub\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        parent::__construct();
        $this->container = new Container();
        $this->router = $this->container->get(Router::class);
    }

    /**
     * @return ServerRequestInterface
     */
    private function visitUrl(string $url, string $method = 'GET')
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('getPath')
            ->willReturn($url);

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $request
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        return $request;
    }

    public function testShouldReturnNotFoundForNonExistingRoute(): void
    {
        // Arrange
        $request = $this->visitUrl('/does-not-exist');

        // Act
        $response = $this->router->route($request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }
}
