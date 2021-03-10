<?php

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
    private $subscriptions = [];

    public function subscribe(Client $client, array $subscriptionRequest)
    {
        $subscription = new Subscription();
        $subscription->client = $client;
        $subscription->callbackId = $subscriptionRequest['callbackId'];
        $subscription->topic = $subscriptionRequest['topic'];
        if ($subscriptionRequest['selector']){
            $subscription->field = $subscriptionRequest['selector']['field'];
            $subscription->operator = $subscriptionRequest['selector']['operator'];
            $subscription->value = $subscriptionRequest['selector']['value'];
        }

        array_push($this->subscriptions, $subscription);
    }

    public function unsubscribe(Client $client, string $callbackId)
    {
        foreach ($this->subscriptions as $subscription) {
            $sameClient = $subscription->client->tokenId === $client->tokenId;
            $sameCallbackId = $subscription->callbackId == $callbackId;

            if ($sameClient && $sameCallbackId) {
                unset($subscription);
            }
        }
    }

    public function forEach(callable $callback): void
    {
        foreach ($this->subscriptions as $subscription) {
            $callback($subscription);
        }
    }

    public function removeClient(Client $client)
    {
        foreach ($this->subscriptions as $subscription) {
            $sameClient = $subscription->client->tokenId === $client->tokenId;
            if ($sameClient) {
                unset($subscription);
            }
        }
    }
}
