<?php

declare(strict_types=1);

namespace GazeHub\Services;

use GazeHub\Models\Client;
use GazeHub\Models\Subscription;

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
        $subscription->field = $subscriptionRequest['field'];
        $subscription->operator = $subscriptionRequest['operator'];
        $subscription->value = $subscriptionRequest['value'];

        array_push($this->subscriptions, $subscription);
    }

    public function unsubscribe(Client $client, array $subscriptionRequest)
    {
        foreach($this->subscriptions as $subscription){
            $sameClient = $subscription->client->tokenId == $client->tokenId;
            $sameTopic = $subscription->topic == $subscriptionRequest['topic'];
            $sameField = $subscription->field == $subscriptionRequest['field'];
            $sameOperator = $subscription->operator == $subscriptionRequest['operator'];
            $sameValue = $subscription->value == $subscriptionRequest['value'];

            if ($sameClient && $sameTopic && $sameField && $sameOperator && $sameValue){
                unset($subscription);
            }
        }
    }

    public function forEach(callable $callback): void
    {
        foreach($this->subscriptions as $subscription) {
            $callback($subscription);
        }
    }

    public function removeClient(Client $client)
    {
        foreach($this->subscriptions as $subscription){
            $sameClient = $subscription->client->tokenId == $client->tokenId;
            if ($sameClient){
                unset($subscription);
            }
        }
    }
}
