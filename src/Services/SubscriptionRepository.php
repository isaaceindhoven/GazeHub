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

use GazeHub\Log;
use GazeHub\Models\Client;
use GazeHub\Models\Subscription;

use function array_filter;
use function array_push;
use function count;
use function in_array;

class SubscriptionRepository
{
    /**
     * @var array
     */
    private $subscriptions = [];

    public function add(Client $client, array $subscriptionRequest)
    {
        foreach ($subscriptionRequest['topics'] as $topic) {
            $subscription = new Subscription();
            $subscription->client = $client;
            $subscription->callbackId = $subscriptionRequest['callbackId'];
            $subscription->topic = $topic;

            array_push($this->subscriptions, $subscription);

            Log::info('Subscribing client to topic', $topic, 'active subscriptions', count($this->subscriptions));
        }
    }

    public function remove(Client $client, array $topics = null)
    {
        foreach ($this->subscriptions as $i => $subscription) {
            $sameClient = $subscription->client->tokenId === $client->tokenId;
            if ($sameClient && ($topics === null || in_array($subscription->topic, $topics))) {
                $topic = $subscription->topic;

                unset($this->subscriptions[$i]);

                Log::info(
                    'Unsubscribing client from topic',
                    $topic,
                    'active subscriptions',
                    count($this->subscriptions)
                );
            }
        }
    }

    public function getSubscriptionsByTopicAndRole(string $topic, string $role = null): array
    {
        return array_filter($this->subscriptions, static function (Subscription $subscription) use ($topic, $role) {
            if ($role !== null) {
                return $subscription->topic === $topic && in_array($role, $subscription->client->roles);
            }

            return $subscription->topic === $topic;
        });
    }
}
