<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use Exception;
use GazeHub\Models\Request;
use GazeHub\Models\Subscription;
use GazeHub\Services\SubscriptionRepository;
use React\Http\Message\Response;

use function array_key_exists;
use function boolval;
use function explode;
use function floatval;
use function in_array;
use function is_array;
use function is_numeric;
use function preg_match;
use function sprintf;

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

        if (!array_key_exists('topic', $data) || !array_key_exists('payload', $data)) {
            return new Response(400, [], 'Missing data');
        }

        $parentScope = $this;

        $this->subscriptionRepository->forEach(static function (Subscription $subscription) use ($data, $parentScope) {
            if ($parentScope->payloadMatchesSubscription($data['topic'], $data['payload'], $subscription)) {
                $subscription->client->stream->write([
                    'callbackId' => $subscription->callbackId,
                    'payload' => $data['payload'],
                ]);
            };
        });

        return new Response(200, [ 'Content-Type' => 'text/html' ], 'Completed');
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod, Generic.Metrics.CyclomaticComplexity.TooHigh, ObjectCalisthenics.Files.FunctionLength.ObjectCalisthenics\Sniffs\Files\FunctionLengthSniff
    private function payloadMatchesSubscription(string $topic, array $payload, Subscription $subscription): bool
    {
        if ($subscription->topic !== $topic) {
            return false;
        }

        try {
            $fieldToCheck = $this->getNestedField($payload, $subscription->field);
        } catch (Exception $e) {
            return false;
        }

        switch ($subscription->operator) {
            case '==':
                return strval($fieldToCheck) === strval($subscription->value);
            case '!=':
                return $fieldToCheck !== $subscription->value;
            case '>':
                return is_numeric($fieldToCheck) && floatval($fieldToCheck) > $subscription->value;
            case '<':
                return is_numeric($fieldToCheck) && floatval($fieldToCheck) < $subscription->value;
            case '>=':
                return is_numeric($fieldToCheck) && floatval($fieldToCheck) >= $subscription->value;
            case '<=':
                return is_numeric($fieldToCheck) && floatval($fieldToCheck) <= $subscription->value;
            case 'in':
                return is_array($fieldToCheck) && in_array($fieldToCheck, $subscription->value);
            case 'regex':
                return boolval(preg_match($subscription->value, $fieldToCheck));
            default:
                return false;
        }
    }

    /**
     * @return mixed
     */
    private function getNestedField(array $obj, string $path)
    {
        $fields = explode('.', $path);

        $fieldToCheck = $obj;

        foreach ($fields as $field) {
            if (!array_key_exists($field, $fieldToCheck)) {
                throw new Exception(sprintf('Field %s not found', $field));
            }
            $fieldToCheck = $fieldToCheck[$field];
        }

        return $fieldToCheck;
    }
}
