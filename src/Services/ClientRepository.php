<?php

/**
  *   Do not remove or alter the notices in this preamble.
  *   This software code regards ISAAC Standard Software.
  *   Copyright © 2021 ISAAC and/or its affiliates.
  *   www.isaac.nl All rights reserved. License grant and user rights and obligations
  *   according to applicable license agreement. Please contact sales@isaac.nl for
  *   questions regarding license and user rights.
  */

declare(strict_types=1);

namespace GazeHub\Services;

use GazeHub\Log;
use GazeHub\Models\Client;
use React\Stream\ThroughStream;

use function array_push;
use function count;
use function json_encode;

class ClientRepository
{
    /**
     * @var Client[]
     */
    private $clients = [];

    public function __construct()
    {
        $this->clients = [];
    }

    public function getByTokenId(string $tokenId): ?Client
    {
        /** @var Client $client */
        foreach ($this->clients as $client) {
            if ($client->tokenId === $tokenId) {
                return $client;
            }
        }

        return null;
    }

    public function add(array $token): Client
    {
        $client = new Client();
        $client->roles = $token['roles'];
        $client->tokenId = $token['jti'];
        $client->stream = new ThroughStream(static function (array $data) {
            Log::info('Sending data to client:', $data);
            return 'data: ' . json_encode($data) . "\n\n";
        });

        array_push($this->clients, $client);
        Log::info('Connected clients', count($this->clients));

        return $client;
    }

    public function remove(Client $clientToRemove): void
    {
        foreach ($this->clients as $index => $client) {
            if ($client->tokenId === $clientToRemove->tokenId) {
                unset($this->clients[$index]);
                break;
            }
        }
        Log::info('Connected clients', count($this->clients));
    }

    public function forEach(callable $callback): void
    {
        /** @var Client $client */
        foreach ($this->clients as $client) {
            $callback($client);
        }
    }
}
