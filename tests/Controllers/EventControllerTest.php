<?php

declare(strict_types=1);

namespace Tests\Controllers;

use DI\Container;
use GazeHub\Controllers\EventController;
use GazeHub\Models\Request;

class EventControllerTest extends ControllerTestCase
{

    public function testShouldReturn401IfNoTokenPresent()
    {
        // Arrange
        $requestMock = $this->getRequestMock();
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn401IfTokenIsEmptyString()
    {
        // Arrange
        $requestMock = $this->getRequestMock();

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn401IfTokenIsInvalid()
    {
        // Arrange
        $requestMock = $this->getRequestMock('INVALID_TOKEN');

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn400IfTopicIsMissingFromBody()
    {
        // Arrange
        $requestMock = $this->getRequestMock($this->serverToken);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['payload' => ['id' => 1, 'name' => 'Shirt']]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturn400IfPayloadIsMissingFromBody()
    {
        // Arrange
        $requestMock = $this->getRequestMock($this->serverToken);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated']);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testShouldReturn200IfTokenIsCorrect()
    {
        // Arrange
        $requestMock = $this->getRequestMock($this->serverToken);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated', 'payload' => ['id' => 1, 'name' => 'Shirt']]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
