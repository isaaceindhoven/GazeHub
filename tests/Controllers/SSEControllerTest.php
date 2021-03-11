<?php

declare(strict_types=1);

namespace Tests\Controllers;

use GazeHub\Controllers\SSEController;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Request;

class SSEControllerTest extends ControllerTestCase
{
    public function testShouldThrowIfNoTokenPresent()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock();
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SSEController::class);
        $controller->handle($request);
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
