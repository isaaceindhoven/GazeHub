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

namespace GazeHub\Controllers;

use GazeHub\Log;
use GazeHub\Models\Request;
use GazeHub\Models\Subscription;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

class EventController extends BaseController
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function handle(Request $request): Response
    {
        $request->isRole('server');

        $validatedData = $request->validate([
            'topic' => 'required|regex:/.+/',
            'payload' => 'nullable',
            'role' => 'regex:/.+/',
        ]);

        Log::debug('Server wants to emit', $validatedData);

        /** @var Subscription[] $subscriptions */
        $subscriptions = $this->subscriptionRepository
            ->getSubscriptionsByTopicAndRole($validatedData['topic'], $validatedData['role']);

        foreach ($subscriptions as $subscription) {
            $subscription->client->send([
                'callbackId' => $subscription->callbackId,
                'payload' => $validatedData['payload'],
            ]);
        }

        return $this->json(['status' => 'Event Send']);
    }
}
