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

namespace Tests\Services;

use GazeHub\Models\Client;
use GazeHub\Services\ClientRepository;
use PHPUnit\Framework\TestCase;
use React\Stream\ThroughStream;

class ClientRepositoryTest extends TestCase
{
    public function testShouldCreateAndStoreClient()
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $stream = new ThroughStream();
        $tokenPayload = ['roles' => ['admin', 'client'], 'jti' => 'randomId'];

        // Act
        $client = $clientRepo->add($stream, $tokenPayload);

        // Assert
        $this->assertEquals(1, $clientRepo->count());
        $this->assertEquals($tokenPayload['roles'], $client->roles);
        $this->assertEquals($tokenPayload['jti'], $client->tokenId);
        $this->assertEquals($stream, $client->stream);
    }

    public function testShouldReturnClientBasedOnTokenId()
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $client = $this->addClientToRepo($clientRepo);

        // Act
        $foundClient = $clientRepo->getByTokenId($client->tokenId);

        // Assert
        $this->assertNotNull($foundClient);
        $this->assertEquals($client, $foundClient);
    }

    public function testShouldRemoveClientFromRepo()
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $client = $this->addClientToRepo($clientRepo);

        // Act
        $clientRepo->remove($client);

        // Assert
        $this->assertEquals(0, $clientRepo->count());
    }

    private function addClientToRepo(ClientRepository $repository): Client
    {
        $stream = new ThroughStream();
        $tokenPayload = ['roles' => ['admin', 'client'], 'jti' => 'randomId'];
        return $repository->add($stream, $tokenPayload);
    }
}
