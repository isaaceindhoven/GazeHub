<?php

namespace GazeHub\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

class CorsMiddleware {

    function handle(ServerRequestInterface $request, callable $next){
        return $next($request)
            ->withHeader('Access-Control-Allow-Origin', (string) $request->getHeaderLine("Origin"))
            ->withHeader('Access-Control-Allow-Methods', "GET, POST, DELETE")
            ->withHeader('Access-Control-Allow-Headers', "X-Requested-With");
    }

}