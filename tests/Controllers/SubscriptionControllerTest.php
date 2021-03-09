<?php

declare(strict_types=1);

namespace Tests\Controllers;

use DI\Container;
use GazeHub\Controllers\SubscriptionController;
use GazeHub\Models\Request;
use GazeHub\Services\ClientRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use React\Stream\ThroughStream;

class SubscriptionControllerTest extends ControllerTestCase
{
    public function testShouldReturn401IfNoTokenPresent()
    {
        // Arrange
        $requestMock = $this->getRequestMock();
        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);

        // Assert
        $response = $controller->create($request);
        $this->assertEquals(401, $response->getStatusCode());

        $response = $controller->destroy($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn401WhenClientIsNotRegisteredAsSseListener()
    {
        // Arrange
        $requestMock = $this->getRequestMock($this->clientToken);
        $requestMock
            ->expects($this->any())
            ->method('getParsedBody')
            ->willReturn(['topic' => 'ProductCreated']);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);

        // Assert
        $response = $controller->create($request);
        $this->assertEquals(401, $response->getStatusCode());

        $response = $controller->destroy($request);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testShouldReturn204IfTokenIsCorrect()
    {
        // Arrange
        $clientRepository = $this->container->get(ClientRepository::class);
        $clientRepository->add(new ThroughStream(), ['roles' => [], 'jti' => 'randomId']);
        $requestMock = $this->getRequestMock($this->clientToken);
        $requestMock
            ->expects($this->any())
            ->method('getParsedBody')
            ->willReturn([[
                'topic' => 'ProductCreated',
                'callbackId' => 'RANDOM',
                'field' => 'id',
                'operator' => '=',
                'value' => '1'
            ]]);

        $request = $this->container->get(Request::class);
        $request->setOriginalRequest($requestMock);

        // Act
        $controller = $this->container->get(SubscriptionController::class);

        // Assert
        $response = $controller->create($request);
        $this->assertEquals(204, $response->getStatusCode());
        $response = $controller->destroy($request);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
