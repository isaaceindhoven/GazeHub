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

use GazeHub\Controllers\EventController;
use GazeHub\Exceptions\DataValidationFailedException;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Models\Subscription;
use GazeHub\Services\SubscriptionRepository;

class EventControllerTest extends ControllerTestCase
{
    public function testShouldThrowIfNotAuthenticated()
    {
        // Arrange
        $this->expectException(UnAuthorizedException::class);

        $subscriptionRepository = $this->createMock(SubscriptionRepository::class);
        $eventController = new EventController($subscriptionRepository);

        $request = $this->createMock(Request::class);
        $request
            ->expects($this->once())
            ->method('isRole')
            ->with('server')
            ->willThrowException(new UnAuthorizedException());

        // Act
        $eventController->handle($request);
    }

    public function testShouldThrowIfTopicIsMissingFromBody()
    {
        // Arrange
        $subRepo = $this->container->get(SubscriptionRepository::class);
        $request = $this->createMock(Request::class);
        $request
            ->expects($this->once())
            ->method('validate')
            ->with([
                'topic' => 'required|regex:/.+/',
                'payload' => 'required',
                'role' => 'regex:/.+/',
            ])
            ->willReturn([
                'topic' => 'test',
                'payload' => [],
                'role' => '',
            ]);

        // Act
        $eventController = new EventController($subRepo);
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

    public function testShouldReturn200EvenIfRoleNotPresent()
    {
        // Arrange
        $requestMock = $this->getRequestMock($this->serverToken);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated', 'payload' => ['id' => 1, 'name' => 'Shirt'], 'role' => '']);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShouldReturn200IfTokenIsCorrect()
    {
        // Arrange
        $requestMock = $this->getRequestMock($this->serverToken);
        $requestMock
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated', 'payload' => ['id' => 1, 'name' => 'Shirt'], 'role' => 'admin']);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $eventController = $this->container->get(EventController::class);

        // Assert
        $response = $eventController->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIfClientSendIsCalled()
    {
        $requestMoch = $this->createMock(Request::class);
        $requestMoch->method('validate')->willReturn([
            'topic' => 'ProductCreated',
            'payload' => ['id' => 1, 'name' => 'Shirt'],
            'role' => 'admin',
        ]);

        $subscriptionRepoMoch = $this->createMock(SubscriptionRepository::class);
        $subscription = new Subscription();
        $subscription->client = $this->createMock(Client::class);
        $subscription->client->expects($this->once())->method('send');
        $subscription->callbackId = 'ABC';
        $subscription->topic = 'ProductCreated';

        $subscriptionRepoMoch->method('getSubscriptionsByTopicAndRole')->willReturn([
            $subscription,
        ]);

        $eventController = new EventController($subscriptionRepoMoch);

        $eventController->handle($requestMoch);
    }
}
