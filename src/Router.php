<?php

namespace GazeHub;

use DI\Container;
use GazeHub\Controllers\EventController;
use GazeHub\Controllers\SSEController;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Router {
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function route(ServerRequestInterface $request) {

        $path = $request->getUri()->getPath();

        $routes = [
            'GET' => [],
            'POST' => [],
            'DELETE' => [],
        ];

        $routes['GET']['/sse'] = [$this->container->get(SSEController::class), 'handle'];
        $routes['POST']['/event'] = [$this->container->get(EventController::class), 'handle'];

        if (array_key_exists($request->getMethod(), $routes) && array_key_exists($path, $routes[$request->getMethod()])) {
            return call_user_func($routes[$request->getMethod()][$path], $request);
        }

        return new Response(404, [], "Not found");
    }
}
