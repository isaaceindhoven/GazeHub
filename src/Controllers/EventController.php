<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Models\Request;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

class EventController extends BaseController
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
        $request->isAuthorized();

        if ($request->getTokenPayload()['role'] !== 'server') {
            throw new UnAuthorizedException();
        }

        $validatedData = $this->validatedData($request->getParsedBody(), [
            'topic' => 'required|string|max:255',
            'payload' => 'required',
        ]);

        foreach ($this->subscriptionRepository->subscriptions as $subscription) {
            if ($validatedData['topic'] !== $subscription->topic) {
                continue;
            }

            $subscription->client->stream->write([
                'callbackId' => $subscription->callbackId,
                'payload' => $validatedData['payload'],
            ]);
        }

        return $this->json(['status' => 'Event Send']);
    }
}
