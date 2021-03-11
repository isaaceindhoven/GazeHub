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

namespace GazeHub\Services;

use GazeHub\Models\Client;
use GazeHub\Models\Subscription;

use function array_push;

class SubscriptionRepository
{
    /**
     * @var array
     */
    public $subscriptions = [];

    public function subscribe(Client $client, array $subscriptionRequest)
    {
        foreach ($subscriptionRequest['topics'] as $topic) {
            $subscription = new Subscription();
            $subscription->client = $client;
            $subscription->callbackId = $subscriptionRequest['callbackId'];
            $subscription->topic = $topic;
            array_push($this->subscriptions, $subscription);
        }
    }

    public function unsubscribe(Client $client, string $callbackId = null)
    {
        foreach ($this->subscriptions as $subscription) {
            $sameClient = $subscription->client->tokenId === $client->tokenId;
            $sameCallbackId = $callbackId !== null ? $subscription->callbackId === $callbackId : true;

            if ($sameClient && $sameCallbackId) {
                unset($subscription);
            }
        }
    }
}
