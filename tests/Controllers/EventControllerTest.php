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

use GazeHub\Models\Client;
use GazeHub\Models\Subscription;
use GazeHub\Services\SubscriptionRepository;

class EventControllerTest extends ControllerTestCase
{
    public function testReponse400IfUnauthorized()
    {
        $this->req('/event', 'POST')->assertHttpCode(401);
    }

    public function testReponse200IfValidRequest()
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test'])
            ->assertHttpCode(200);
    }

    public function testResponse400IfTopicMissing()
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => '', 'payload' => 'Test'])
            ->assertHttpCode(400);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['payload' => 'Test'])
            ->assertHttpCode(400);
    }

    public function testResponse200IfPayloadMissing()
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => ''])
            ->assertHttpCode(200);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => []])
            ->assertHttpCode(200);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated'])
            ->assertHttpCode(200);
    }

    public function testReponse200IfRoleIsMissing()
    {
        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test', 'role' => ''])
            ->assertHttpCode(200);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test'])
            ->assertHttpCode(200);
    }

    public function testIfClientSendIsCalled()
    {
        $subscriptionRepoMoch = $this->createMock(SubscriptionRepository::class);
        $subscription = new Subscription();
        $subscription->client = $this->createMock(Client::class);
        $subscription->client->expects($this->once())->method('send');
        $subscription->callbackId = 'ABC';
        $subscription->topic = 'ProductCreated';

        $subscriptionRepoMoch->method('getSubscriptionsByTopicAndRole')->willReturn([
            $subscription,
        ]);

        $this->container->set(SubscriptionRepository::class, $subscriptionRepoMoch);

        $this
            ->req('/event', 'POST')
            ->asServer()
            ->setBody(['topic' => 'ProductCreated', 'payload' => 'Test'])
            ->assertHttpCode(200);
    }
}
