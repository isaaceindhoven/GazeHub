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

use GazeHub\Controllers\SubscriptionController;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use React\Stream\ThroughStream;

class SubscriptionControllerTest extends ControllerTestCase
{
    public function testSubscribeShouldThrowIfNoTokenPresent()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock();
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);

        // Assert
        $controller->create($request);
    }

    public function testUnsubscribeShouldThrowIfNoTokenPresent()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock();
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);

        // Assert
        $controller->destroy($request);
    }

    public function testSubscribeShouldThrowWhenClientIsNotRegistered()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock($this->clientToken);
        $requestMock
            ->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated']);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);
        $controller->create($request);
    }

    public function testUnsubscribeShouldThrowWhenClientIsNotRegistered()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);
        $requestMock = $this->getRequestMock($this->clientToken);
        $requestMock
            ->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated']);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);
        $controller->destroy($request);
    }

    public function testSubscribeShouldReturn204IfTokenIsCorrect()
    {
        // Arrange
        $clientRepository = $this->container->get(ClientRepository::class);
        $clientRepository->add(new ThroughStream(), ['roles' => [], 'jti' => 'randomId']);
        $requestMock = $this->getRequestMock($this->clientToken);
        $requestMock
            ->expects($this->any())
            ->method('getParsedBody')
            ->willReturn([
                'callbackId' => 'RANDOM',
                'topics' => ['ProductCreated'],
            ]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);

        // Assert
        $response = $controller->create($request);
        $this->assertEquals(200, $response->getStatusCode());
        // $response = $controller->destroy($request);
        // $this->assertEquals(204, $response->getStatusCode());
    }

    public function testUnsubscribeShouldReturn204IfTokenIsCorrect()
    {
        // Arrange
        $clientRepository = $this->container->get(ClientRepository::class);
        $clientRepository->add(new ThroughStream(), ['roles' => [], 'jti' => 'randomId']);
        $requestMock = $this->getRequestMock($this->clientToken);
        $requestMock
            ->expects($this->any())
            ->method('getParsedBody')
            ->willReturn([
                'callbackId' => 'RANDOM',
                'topics' => ['ProductCreated'],
            ]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);

        // Assert
        $response = $controller->destroy($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
