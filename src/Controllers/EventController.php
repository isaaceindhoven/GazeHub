<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use Firebase\JWT\JWT;
use GazeHub\Models\Client;
use GazeHub\Services\ConfigRepository;
use GazeHub\Services\ClientRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Throwable;

use function file_get_contents;
use function str_replace;

class EventController
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
        $this->config = $config;
        $this->clientRepository = $clientRepository;
        $this->publicKey = file_get_contents($config->get('jwt_public_key'));
    }

    /**
     * @return Response
     */
    public function handle(ServerRequestInterface $request)
    {

        if ($request->hasHeader('Authorization') === false) {
            return new Response(401);
        }

        $token = str_replace('Bearer ', '', $request->getHeaderLine('Authorization'));

        try {
            $decoded = JWT::decode($token, $this->publicKey, ['RS256']);
            if ($decoded->role !== 'server') {
                return new Response(401);
            }

            $data = (string) $request->getBody();

            $this->clientRepository->forEach(static function (Client $client) use ($data) {
                $client->stream->write($data);
            });

            return new Response(200, [ 'Content-Type' => 'text/html' ], 'Completed');
        } catch (Throwable $th) {
            return new Response(401);
        }
    }
}
