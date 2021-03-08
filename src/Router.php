<?php

namespace GazeHub;

use DI\Container;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class Router {
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function route(ServerRequestInterface $request) {

        $path = $request->getUri()->getPath();

        $routes = require(__DIR__ . '/../config/Routes.php');

        $method = $request->getMethod();

        $endPointExist = array_key_exists($method, $routes) && (array_key_exists($path, $routes[$method]));

        if ($endPointExist){
            $endPoint = $routes[$method][$path];
            $handler = [ $this->container->get($endPoint[0]), $endPoint[1] ];
            return call_user_func($handler, $request);
        }

        return new Response(404, [], "Not found");
    }
}
