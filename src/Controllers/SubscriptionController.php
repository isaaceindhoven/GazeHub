<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use GazeHub\Models\Client;
use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

use function array_key_exists;
use function count;
use function is_array;

class SubscriptionController
{
    /**
     *  @var SubscriptionRepository
     */
    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
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

        return $this->getTopicFromRequest(
            $request,
            static function (Client $client, array $subscriptionRequest) use ($scope) {
                $scope->subscriptionRepository->subscribe($client, $subscriptionRequest);
            }
        );
    }

    public function destroy(Request $request): Response
    {
        $scope = $this;

        return $this->getTopicFromRequest(
            $request,
            static function (Client $client, array $subscriptionRequest) use ($scope) {
                $scope->subscriptionRepository->unsubscribe($client, $subscriptionRequest);
            }
        );
    }

    private function getTopicFromRequest(Request $request, callable $callback): Response
    {


        if (!$request->isAuthorized()) {
            return new Response(401);
        }

        if (!is_array($request->getParsedBody()) || count($request->getParsedBody()) === 0) {
            return new Response(400, [], 'Missing topics');
        }


        $client = $this->clientRepository->getByTokenId($request->getTokenPayload()['jti']);

        if (!$client) {
            return new Response(401);
        }

        foreach ($request->getParsedBody() as $subscriptionRequest) {
            if (!$this->arrayHasAllKeys($subscriptionRequest, ['topic', 'callbackId'])) {
                return new Response(400, [], 'Missing data');
            }
            $callback($client, $subscriptionRequest);
        }

        return new Response(204);
    }

    private function arrayHasAllKeys(array $arr, array $keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $arr)) {
                return false;
            }
        }
        return true;
    }
}
