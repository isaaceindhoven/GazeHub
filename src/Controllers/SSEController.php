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
use GazeHub\Services\ClientRepository;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;
use React\Stream\ThroughStream;

use function json_encode;

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

        $stream = new ThroughStream(static function (array $data) {
            Log::info('Sending data to client:', $data);
            return 'data: ' . json_encode($data) . "\n\n";
        });

        $client = $this->clientRepository->add($stream, $request->getTokenPayload());

        $scope = $this;

        $stream->on('close', static function () use ($scope, $client) {
            $scope->subscriptionRepository->unsubscribe($client);
            $scope->clientRepository->remove($client);
        });

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $stream);
    }
}
