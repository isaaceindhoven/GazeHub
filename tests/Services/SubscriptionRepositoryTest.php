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

namespace GazeHub\Tests\Services;

use GazeHub\Models\Client;
use GazeHub\Services\SubscriptionRepository;
use PHPUnit\Framework\TestCase;
use React\Stream\ThroughStream;

use function count;
use function uniqid;

class SubscriptionRepositoryTest extends TestCase
{
    public function testShouldAddSubscription()
    {
        // Arrange
        $subscriptionRepository = new SubscriptionRepository();
        $client1 = $this->createClient();
        $subscriptionRequest = ['topics' => ['ABC'], 'callbackId' => uniqid()];

        // Act
        $subscriptionRepository->add($client1, $subscriptionRequest);

        // Assert
        $subs = $subscriptionRepository->getSubscriptionsByTopic('ABC');
        $this->assertEquals(1, count($subs));
        $this->assertEquals($subs[0]->client->tokenId, $client1->tokenId);
        $this->assertEquals($subs[0]->callbackId, $subscriptionRequest['callbackId']);
    }

    public function testShouldRemoveAllClientSubscriptions()
    {
        // Arrange
        $subscriptionRepository = new SubscriptionRepository();
        $client1 = $this->createClient();
        $subscriptionRequest1 = ['topics' => ['ABC'], 'callbackId' => uniqid()];
        $client2 = $this->createClient();
        $subscriptionRequest2 = ['topics' => ['ABC'], 'callbackId' => uniqid()];

        // Act
        $subscriptionRepository->add($client1, $subscriptionRequest1);
        $subscriptionRepository->add($client2, $subscriptionRequest2);
        $subscriptionRepository->remove($client1);

        // Assert
        $subs = $subscriptionRepository->getSubscriptionsByTopic('ABC');
        $this->assertEquals(1, count($subs));
        $this->assertEquals($subs[1]->client->tokenId, $client2->tokenId);
        $this->assertEquals($subs[1]->callbackId, $subscriptionRequest2['callbackId']);
    }

    public function testShouldRemoveSingleSubscriptions()
    {
        // Arrange
        $subscriptionRepository = new SubscriptionRepository();
        $client1 = $this->createClient();
        $subscriptionRequest1 = ['topics' => ['1'], 'callbackId' => uniqid()];
        $subscriptionRequest2 = ['topics' => ['1'], 'callbackId' => uniqid()];

        // Act
        $subscriptionRepository->add($client1, $subscriptionRequest1);
        $subscriptionRepository->add($client1, $subscriptionRequest2);
        $subscriptionRepository->remove($client1, $subscriptionRequest1['callbackId']);

        // Assert
        $subs = $subscriptionRepository->getSubscriptionsByTopic('1');
        $this->assertEquals(1, count($subs));

        $this->assertEquals($subs[1]->client->tokenId, $client1->tokenId);
        $this->assertEquals($subs[1]->callbackId, $subscriptionRequest2['callbackId']);
    }

    private function createClient(array $roles = []): Client
    {
        $client = new Client();
        $client->stream = new ThroughStream();
        $client->roles = $roles;
        $client->tokenId = uniqid();
        return $client;
    }
}
