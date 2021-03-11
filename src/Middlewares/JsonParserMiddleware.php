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

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class JsonParserMiddleware
{
    public function handle(ServerRequestInterface $request, callable $next): Response
    {
        if ($request->getHeaderLine('Content-Type') === 'application/json') {
            try {
                $data = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);

                if (!$data) {
                    $data = [];
                }
            } catch (Exception $e) {
                return new Response(400, [], 'Invalid JSON');
            }

            $request = $request->withParsedBody($data);
        }

        return $next($request);
    }
}
