<?php

namespace GazeHub;

use DI\Container;
use GazeHub\Controllers\EventController;
use GazeHub\Controllers\SSEController;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Router {

    private $container;

    function __construct(Container $container) {
        $this->container = $container;
    }

    function route(ServerRequestInterface $request) {

        $path = $request->getUri()->getPath();

        $routes = [];
        $routes["/sse"] = [$this->container->get(SSEController::class), 'handle'];
        $routes["/event"] = [$this->container->get(EventController::class), 'handle'];

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
