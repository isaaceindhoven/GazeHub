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

use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

class SSEController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var SubscriptionRepository
     */
    //phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $subscriptionRepository;

    public function __construct(ClientRepository $clientRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function handle(Request $request): Response
    {
        $request->isAuthorized();

        $payload = $request->getTokenPayload();

        $client = $this->clientRepository->add($payload['roles'], $payload['jti']);

        $scope = $this;

        $client->stream->on('close', static function () use ($scope, $client) {
            $scope->subscriptionRepository->remove($client);
            $scope->clientRepository->remove($client);
        });

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $client->stream);
    }
}
