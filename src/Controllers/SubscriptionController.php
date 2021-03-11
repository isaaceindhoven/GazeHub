<?php

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

        $validatedData = $this->validatedData($request->getParsedBody(), [
            'callbackId' => 'required|string',
            'topics' => 'required|array:string|not_empty',
        ]);

        $this->subscriptionRepository->subscribe($client, $validatedData);

        return $this->json(['status' => 'subscribed'], 200);
    }

    public function destroy(Request $request): Response
    {
        $client = $this->getClient($request);

        $validatedData = $this->validatedData($request->getParsedBody(), [
            'callbackId' => 'required|string',
        ]);

        $this->subscriptionRepository->unsubscribe($client, $validatedData['callbackId']);

        return $this->json(['status' => 'unsubscribed']);
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
