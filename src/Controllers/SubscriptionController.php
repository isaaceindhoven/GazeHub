<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Models\Subscription;
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

        return $this->getTopicFromRequest($request, static function(Client $client, array $subscriptionRequest) use ($scope) {
            $scope->subscriptionRepository->subscribe($client, $subscriptionRequest);
        });
    }

    public function destroy(Request $request): Response
    {
        $scope = $this;

        return $this->getTopicFromRequest($request, static function(Client $client, array $subscriptionRequest) use ($scope) {
            $scope->subscriptionRepository->unsubscribe($client, $subscriptionRequest);
        });
    }

    private function getTopicFromRequest(Request $request, callable $callback): Response
    {
        if (!$request->isAuthorized()) {
            return new Response(401);
        }

        $body = $request->getParsedBody();

        if (!is_array($body)){
            return new Response(400, [], 'Missing topics');
        }

        if (count($body) == 0){
            return new Response(400, [], 'Missing topics');
        }

        $client = $this->clientRepository->getByTokenId($request->getTokenPayload()['jti']);

        if (!$client) {
            return new Response(401);
        }

        foreach($body as $subscriptionRequest){

            if (!array_key_exists('topic', $subscriptionRequest)) {
                return new Response(400, [], 'Missing topic');
            }

            if (!array_key_exists('callbackId', $subscriptionRequest)) {
                return new Response(400, [], 'Missing callbackId');
            }

            $callback($client, $subscriptionRequest);
        }
        
        return new Response(204);
    }

}
