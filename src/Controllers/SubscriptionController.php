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

use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

class SubscriptionController extends BaseController
{
    /**
     *  @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     *  @var ClientRepository
     */
    private $clientRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository, ClientRepository $clientRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->clientRepository = $clientRepository;
    }

    public function create(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'callbackId' => 'required|regex:/.+/',
            'topics' => 'required|array',
            'topics.*' => 'required|regex:/.+/',
        ]);

        $this->subscriptionRepository->add($client, $validatedData['topics'], $validatedData['callbackId']);

        return $this->json(['status' => 'subscribed'], 200);
    }

    public function destroy(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $request->validate([
            'topics' => 'required|array',
            'topics.*' => 'required|regex:/.+/',
        ]);

        $this->subscriptionRepository->remove($client, $validatedData['topics']);

        return $this->json(['status' => 'unsubscribed']);
    }

    public function ping(Request $request): Response
    {
        $client = $this->getClient($request);
        $client->send(['pong']);
        return new Response();
    }

    protected function getClient(Request $request): Client
    {
        $request->isAuthorized();
        $client = $this->clientRepository->getByTokenId($request->getTokenPayload()['jti']);
        if (!$client) {
            throw new UnAuthorizedException();
        }
        return $client;
    }
}
