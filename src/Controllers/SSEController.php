<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use Firebase\JWT\JWT;
use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\ConfigRepository;
use GazeHub\Services\SubscriptionRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Stream\ThroughStream;
use Throwable;

class SSEController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var string
     */
    private $publicKey;

    public function __construct(
        ClientRepository $clientRepository,
        ConfigRepository $config,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->publicKey = file_get_contents($config->get('jwt_public_key'));
    }

    public function handle(Request $request): Response
    {
        $token = $request->getQueryParams()['token'] ?? null;

        if (!$token) {
            return new Response(401);
        }

        try {
            $decoded = JWT::decode($token, $this->publicKey, ['RS256']);
        } catch (Throwable $th) {
            return new Response(401);
        }

        $stream = new ThroughStream(static function (array $data) {
            return 'data: ' . json_encode($data) . "\n\n";
        });

        $client = $this->clientRepository->add($stream, $decoded);

        $scope = $this;

        $stream->on('close', static function () use ($scope, $client) {
            $scope->subscriptionRepository->removeClient($client);
            $scope->clientRepository->remove($client);
        });

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $stream);
    }
}
