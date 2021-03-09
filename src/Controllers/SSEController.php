<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use Firebase\JWT\JWT;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\ConfigRepository;
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
     * @var string
     */
    private $publicKey;

    public function __construct(ClientRepository $clientRepository, ConfigRepository $config)
    {
        $this->clientRepository = $clientRepository;
        $this->publicKey = file_get_contents($config->get('jwt_public_key'));
    }

    /**
     * @return Response
     */
    public function handle(ServerRequestInterface $request)
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

        $stream = new ThroughStream(static function ($data) {
            return 'data: ' . $data . "\n\n";
        });

        $client = $this->clientRepository->add($stream, $decoded);

        $scope = $this;

        $stream->on('close', static function () use ($scope, $client) {
            $scope->clientRepository->remove($client);
        });

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $stream);
    }
}
