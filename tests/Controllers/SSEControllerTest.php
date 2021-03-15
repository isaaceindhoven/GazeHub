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

namespace GazeHub\Tests\Controllers;

use GazeHub\Controllers\SSEController;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\SubscriptionRepository;
use React\Stream\ThroughStream;

use function uniqid;

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

    public function testSSEConnectionClosed()
    {
        $clientRepositoryMock = $this->createMock(ClientRepository::class);
        $subscriptionRepositoryMock = $this->createMock(SubscriptionRepository::class);
        $requestMock = $this->createMock(Request::class);
        $requestMock->method('getTokenPayload')->willReturn(['roles' => ['admin'], 'jti' => uniqid()]);

        $client = new Client();
        $client->stream = new ThroughStream();

        $clientRepositoryMock->method('add')->willReturn($client);

        $clientRepositoryMock->expects($this->once())->method('remove');
        $subscriptionRepositoryMock->expects($this->once())->method('remove');

        $sseController = new SSEController($clientRepositoryMock, $subscriptionRepositoryMock);

        $sseController->handle($requestMock);

        $client->stream->end();
    }
}
