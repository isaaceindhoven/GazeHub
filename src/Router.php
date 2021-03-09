<?php

declare(strict_types=1);

namespace GazeHub;

use DI\Container;
use GazeHub\Models\Request;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

use function array_key_exists;
use function call_user_func;

class Router
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function route(ServerRequestInterface $request): Response
    {

        $path = $request->getUri()->getPath();

        $routes = require(__DIR__ . '/../config/routes.php');

        $method = $request->getMethod();

        $endPointExist = array_key_exists($method, $routes) && (array_key_exists($path, $routes[$method]));

        $req = $this->container->get(Request::class);
        $req->setOriginalRequest($request);

        if ($endPointExist) {
            $endPoint = $routes[$method][$path];
            $handler = [ $this->container->get($endPoint[0]), $endPoint[1] ];
            return call_user_func($handler, $req);
        }

        return new Response(404, [], 'Not found');
    }
}
