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

use GazeHub\Models\Client;
use React\Stream\ThroughStream;
use SplObjectStorage;

class ClientRepository
{
    /**
     * @var SplObjectStorage
     */
    private $clients;

    public function __construct()
    {
        $this->clients = new SplObjectStorage();
    }

    public function getByTokenId(string $tokenId): ?Client
    {
        foreach ($this->clients as $client) {
            if ($client->tokenId === $tokenId) {
                return $client;
            }
        }

        return null;
    }

    public function add(ThroughStream $stream, array $token): Client
    {
        $client = new Client();
        $client->stream = $stream;
        $client->roles = $token['roles'];
        $client->tokenId = $token['jti'];

        $this->clients->attach($client);

        return $client;
    }

    public function remove(Client $client): void
    {
        $this->clients->detach($client);
    }

    public function forEach(callable $callback): void
    {
        foreach ($this->clients as $stream) {
            $callback($stream);
        }
    }
}
