<?php

declare(strict_types=1);

namespace Tests\Controllers;

use DI\Container;
use GazeHub\Controllers\SSEController;
use GazeHub\Models\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class SSEControllerTest extends ControllerTestCase
{

    public function testShouldReturn401IfNoTokenPresent()
    {
        // Arrange
        $requestMock = $this->getRequestMock();
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SSEController::class);

        // Assert
        $response = $controller->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn200IfTokenIsCorrect()
    {
        // Arrange
        $requestMock = $this->getRequestMock($this->clientToken);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SSEController::class);

        // Assert
        $response = $controller->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    
}
