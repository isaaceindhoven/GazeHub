<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use DI\Container;
use GazeHub\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class RouterTest extends TestCase {

    private $router;
    private $container;

    public function __construct(){
        parent::__construct();
        $this->container = new Container();
        $this->router = $this->container->get(Router::class);
    }

    private function visitUrl($url, $method = "GET"){
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
    
    public function testShouldReturnNotFoundForNonExistingRoute() {
        // Arrange
        $request = $this->visitUrl("/does-not-exist");

        // Act
        $response = $this->router->route($request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testShouldNotReturn404IfRouteExists() {
        // Arrange
        $request = $this->visitUrl("/event", "POST");

        // Act
        $response = $this->router->route($request);

        // Assert
        $this->assertNotEquals(404, $response->getStatusCode());
    }
}
