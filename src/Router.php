<?php

namespace GazeHub;

use GazeHub\Controllers\EventController;
use GazeHub\Controllers\SSEController;
use GazeHub\Services\StreamRepository;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Router {

    private $streamRepository;

    function __construct(StreamRepository $streamRepository) {
        $this->streamRepository = $streamRepository;
    }

    function route(ServerRequestInterface $request) {

        $path = $request->getUri()->getPath();

        $routes = [];
        $routes["/sse"] = [new SSEController($this->streamRepository), 'handle'];
        $routes["/event"] = [new EventController($this->streamRepository), 'handle'];

        // TODO: remove
        if ($request->getUri()->getPath() === '/') {
            return new Response(200, [ 'Content-Type' => 'text/html' ], file_get_contents(__DIR__ . '/../client.html'));
        }

        if (array_key_exists($path, $routes)) {
            return call_user_func($routes[$path], $request);
        }

        return new Response(404, [], "Not found");
        
    }
}