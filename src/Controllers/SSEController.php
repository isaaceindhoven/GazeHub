<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

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
    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $subscriptionRepository;

    public function __construct(
        ClientRepository $clientRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function handle(Request $request): Response
    {
        if (!$request->isAuthorized()) {
            return new Response(401);
        }

        $stream = new ThroughStream(static function (array $data) {
            return 'data: ' . json_encode($data) . "\n\n";
        });

        $client = $this->clientRepository->add($stream, $request->getTokenPayload());

        $scope = $this;

        $stream->on('close', static function () use ($scope, $client) {
            $scope->subscriptionRepository->removeClient($client);
            $scope->clientRepository->remove($client);
        });

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $stream);
    }
}
