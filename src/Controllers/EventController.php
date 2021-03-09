<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

use function array_key_exists;

class EventController
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
        if (!$request->isAuthorized() || $request->getTokenPayload()['role'] !== 'server') {
            return new Response(401);
        }

        $data = $request->getParsedBody();

        if (!array_key_exists('topic', $data)) {
            return new Response(400, [], 'Missing topic');
        }

        if (!array_key_exists('payload', $data)) {
            return new Response(400, [], 'Missing payload');
        }

        $this->subscriptionRepository->forEachInTopic($data['topic'], static function (Client $client) use ($data) {
            $client->stream->write($data['payload']);
        });

        return new Response(200, [ 'Content-Type' => 'text/html' ], 'Completed');
    }
}
