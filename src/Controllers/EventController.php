<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use Exception;
use GazeHub\Models\Request;
use GazeHub\Models\Subscription;
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

        $parentScope = $this;

        $this->subscriptionRepository->forEach(static function (Subscription $subscription) use ($data, $parentScope) {
            if ($parentScope->payloadMatchesSubscription($data['topic'], $data['payload'], $subscription)){
                $subscription->client->stream->write([
                    "callbackId" => $subscription->callbackId,
                    "payload" => $data['payload']
                ]);
            };
        });

        return new Response(200, [ 'Content-Type' => 'text/html' ], 'Completed');
    }

    private function payloadMatchesSubscription($topic, $payload, $subscription){
        if ($subscription->topic != $topic) return false;

        $fields = explode(".", $subscription->field);

        $fieldToCheck = $payload;

        foreach($fields as $field){
            if (!array_key_exists($field, $fieldToCheck)) return false;
            $fieldToCheck = $fieldToCheck[$field];
        }

        switch ($subscription->operator) {
            case "==": return $fieldToCheck == $subscription->value;
            case "!=": return $fieldToCheck != $subscription->value;
            case ">": return is_numeric($fieldToCheck) && floatval($fieldToCheck) > $subscription->value;
            case "<": return is_numeric($fieldToCheck) && floatval($fieldToCheck) < $subscription->value;
            case ">=": return is_numeric($fieldToCheck) && floatval($fieldToCheck) >= $subscription->value;
            case "<=": return is_numeric($fieldToCheck) && floatval($fieldToCheck) <= $subscription->value;
            case "in": return is_array($fieldToCheck) && in_array($fieldToCheck, $subscription->value);
            case "regex": return boolval(preg_match($subscription->value, $fieldToCheck));
            default: return false;
        }
    }
}
