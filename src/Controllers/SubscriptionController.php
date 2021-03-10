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
        if (!$request->isAuthorized()) {
            return new Response(401);
        }

        $client = $this->clientRepository->getByTokenId($request->getTokenPayload()['jti']);

        if (!$client) {
            return new Response(401);
        }

        if (!$this->arrayHasAllKeys($request->getParsedBody(), ["callbackId", "topic", "selector"])){
            return new Response(400, [], 'Subscribe is missing data');
        }

        $this->subscriptionRepository->subscribe($client, $request->getParsedBody());

        return new Response(204);
    }

    public function destroy(Request $request): Response
    {
        if (!$request->isAuthorized()) {
            return new Response(401);
        }

        $client = $this->clientRepository->getByTokenId($request->getTokenPayload()['jti']);

        if (!$client) {
            return new Response(401);
        }

        if (!$this->arrayHasAllKeys($request->getParsedBody(), ["callbackId"])){
            return new Response(400, [], 'Unsubscribe is callbackId');
        }

        $this->subscriptionRepository->unsubscribe($client, $request->getParsedBody()["callbackId"]);

        return new Response(204);
    }

    private function arrayHasAllKeys(array $arr, array $keys): bool
    {
        if (!is_array($arr)){
            return false;
        }

        foreach ($keys as $key) {
            if (!array_key_exists($key, $arr)) {
                return false;
            }
        }
        return true;
    }
}
