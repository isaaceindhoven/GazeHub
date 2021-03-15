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

namespace GazeHub\Tests\Middlewares;

use GazeHub\Middlewares\CorsMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class CorsMiddlewareTest extends TestCase
{
    public function testShouldAddCorsHeadersToOptionsRequestAndRespondWith204()
    {
        // Arrange
        $middleware = new CorsMiddleware();
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('options');
        $callable = static function (ServerRequestInterface $req) {
            return $req;
        };

        // Act
        $response = $middleware->handle($request, $callable);

        // Assert
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertArrayHasKey('Access-Control-Allow-Origin', $response->getHeaders());
        $this->assertArrayHasKey('Access-Control-Allow-Methods', $response->getHeaders());
        $this->assertArrayHasKey('Access-Control-Allow-Headers', $response->getHeaders());
    }

    public function testShouldAddCorsHeadersToOtherRequestAndForwardRequest()
    {
        // Arrange
        $middleware = new CorsMiddleware();
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn('get');
        $callable = static function (ServerRequestInterface $req) {
            $resp = new Response(500);
            return $resp->withHeader('X-Test-Header', 'This is a test');
        };

        // Act
        $response = $middleware->handle($request, $callable);

        // Assert
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertArrayHasKey('X-Test-Header', $response->getHeaders());
        $this->assertArrayHasKey('Access-Control-Allow-Origin', $response->getHeaders());
        $this->assertArrayHasKey('Access-Control-Allow-Methods', $response->getHeaders());
        $this->assertArrayHasKey('Access-Control-Allow-Headers', $response->getHeaders());
    }
}
