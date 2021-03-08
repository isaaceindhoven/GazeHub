<?php
declare(strict_types=1);

use DI\Container;
use GazeHub\Router;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouterTest extends TestCase {
    public function testShouldReturnNotFoundForNonExistingRoute() {
        // Arrange
        $container = new Container();
        $router = $container->get(Router::class);
        
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects($this->once())
            ->method('getPath')
            ->willReturn('/non-existing-route');

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);
        
        $request
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn('GET');

        // Act
        $response = $router->route($request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }
}
