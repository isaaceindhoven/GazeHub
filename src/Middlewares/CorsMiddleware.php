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

namespace GazeHub\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class CorsMiddleware
{
    public function handle(ServerRequestInterface $request, callable $next): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            return $this->addCorsHeaders($request, new Response(204));
        }

        return $this->addCorsHeaders($request, $next($request));
    }

    private function addCorsHeaders(ServerRequestInterface $request, Response $response): Response
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', (string) $request->getHeaderLine('Origin'))
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Authorization, Content-Type');
    }
}
