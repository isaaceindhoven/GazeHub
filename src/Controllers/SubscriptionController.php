<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

class SubscriptionController
{
    /**
     *  @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository, ClientRepository $clientRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->clientRepository = $clientRepository;
    }

    public function create(Request $request): Response
    {   
        $scope = $this;

        return $this->getTopicFromRequest($request, static function(string $topic, Client $client) use ($scope) {
            $scope->subscriptionRepository->subscribe($topic, $client);
        });
    }

    public function destroy(Request $request): Response
    {
        $scope = $this;

        return $this->getTopicFromRequest($request, static function(string $topic, Client $client) use ($scope) {
            $scope->subscriptionRepository->unsubscribe($topic, $client);
        });
    }

    private function getTopicFromRequest(Request $request, callable $callback): Response
    {
        if (!$request->isAuthorized()) {
            return new Response(401);
        }

        $body = $request->getParsedBody();
        
        if (!array_key_exists('topic', $body)) {
            return new Response(400, [], 'Missing topic');
        }
        
        $client = $this->clientRepository->getByTokenId($request->getTokenPayload()['jti']);

        if (!$client) {
            return new Response(401);
        }
        
        $callback($body['topic'], $client);
        
        return new Response(204);
    }
}
