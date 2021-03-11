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

namespace Tests\Controllers;

use GazeHub\Controllers\EventController;
use GazeHub\Exceptions\DataValidationFailedException;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Request;

class EventControllerTest extends ControllerTestCase
{
    public function testShouldThrowIfNoTokenPresent()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock();
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);
        $eventController->handle($request);
    }

    public function testShouldThrowIfTokenIsEmptyString()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock();

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);
        $eventController->handle($request);
    }

    public function testShouldThrowIfTokenIsInvalid()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock('INVALID_TOKEN');

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);
        $eventController->handle($request);
    }

    public function testShouldThrowIfTopicIsMissingFromBody()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $requestMock = $this->getRequestMock($this->serverToken);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['payload' => ['id' => 1, 'name' => 'Shirt']]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);
        $eventController->handle($request);
    }

    public function testShouldThrowIfPayloadIsMissingFromBody()
    {
        // Arrange
        $this->expectException(DataValidationFailedException::class);
        $requestMock = $this->getRequestMock($this->serverToken);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['topics' => ['ProductCreated']]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);
        $eventController->handle($request);
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
