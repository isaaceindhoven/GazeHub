<?php

declare(strict_types=1);

namespace GazeHub\Services;

use GazeHub\Models\Client;
use SplObjectStorage;

class SubscriptionRepository
{
    /**
     * @var SplObjectStorage[]
     */
    private $subscriptions = [];

    public function subscribe(string $topic, Client $client)
    {
        if (!array_key_exists($topic, $this->subscriptions)) {
            $this->subscriptions[$topic] = new SplObjectStorage();
        }

        $this->subscriptions[$topic]->attach($client);
    }

    public function unsubscribe(string $topic, Client $client)
    {
        if (array_key_exists($topic, $this->subscriptions)) {
            $this->subscriptions[$topic]->detach($client);
        }
    }

    public function forEachInTopic(string $topic, callable $callback): void
    {
        if (array_key_exists($topic, $this->subscriptions)) {
            foreach($this->subscriptions[$topic] as $client) {
                $callback($client);
            }
        }
    }

    public function removeClient(Client $client)
    {
        foreach($this->subscriptions as $topic) {
            if ($topic->contains($client)) {
                $topic->detach($client);
            }
        }
    }
}
