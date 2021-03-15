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

namespace GazeHub\Tests\Services;

use GazeHub\Models\Client;
use GazeHub\Services\ClientRepository;
use PHPUnit\Framework\TestCase;

use function uniqid;

class ClientRepositoryTest extends TestCase
{
    public function testShouldCreateAndStoreClient()
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $tokenPayload = ['roles' => ['admin', 'client'], 'jti' => 'randomId'];

        // Act
        $client = $clientRepo->add($tokenPayload);

        // Assert
        $this->assertEquals($tokenPayload['roles'], $client->roles);
        $this->assertEquals($tokenPayload['jti'], $client->tokenId);
    }

    public function testShouldReturnClientBasedOnTokenId()
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $client1 = $this->addClientToRepo($clientRepo);
        $this->addClientToRepo($clientRepo);

        // Act
        $foundClient = $clientRepo->getByTokenId($client1->tokenId);

        // Assert
        $this->assertNotNull($foundClient);
        $this->assertEquals($client1, $foundClient);
    }

    public function testShouldRemoveClientFromRepo()
    {
        // Arrange
        $clientRepo = new ClientRepository();
        $client1 = $this->addClientToRepo($clientRepo);
        $client2 = $this->addClientToRepo($clientRepo);

        // Act
        $clientRepo->remove($client1);

        // Assert
        $this->assertNull($clientRepo->getByTokenId($client1->tokenId));
        $this->assertEquals($client2, $clientRepo->getByTokenId($client2->tokenId));
    }

    private function addClientToRepo(ClientRepository $repository): Client
    {
        $tokenPayload = ['roles' => ['admin', 'client'], 'jti' => uniqid()];
        return $repository->add($tokenPayload);
    }
}
