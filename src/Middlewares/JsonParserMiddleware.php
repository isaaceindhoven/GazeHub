<?php

declare(strict_types=1);

namespace GazeHub\Middlewares;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class JsonParserMiddleware
{
    public function handle(ServerRequestInterface $request, callable $next): Response
    {
        if ($request->getHeaderLine('Content-Type') == 'application/json') {
            try {
                $data = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR) ?? [];
            } catch(Exception $e) {
                return new Response(400, [], 'Invalid JSON');
            }

            $request = $request->withParsedBody($data);
        }

        return $next($request);
    }
}
