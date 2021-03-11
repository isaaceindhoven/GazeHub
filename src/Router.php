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

namespace GazeHub;

use DI\Container;
use GazeHub\Exceptions\DataValidationFailedException;
use GazeHub\Models\Request;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

use function array_key_exists;
use function call_user_func;
use function json_encode;

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
        $method = $request->getMethod();

        $routes = require(__DIR__ . '/../config/routes.php');

        $endPointExist = array_key_exists($method, $routes) && (array_key_exists($path, $routes[$method]));

        if (!$endPointExist) {
            return new Response(404, [], 'Not found');
        }

        $req = $this->container->get(Request::class);
        $req->setOriginalRequest($request);

        $endPoint = $routes[$method][$path];
        $handler = [ $this->container->get($endPoint[0]), $endPoint[1] ];

        try {
            return call_user_func($handler, $req);
        } catch (DataValidationFailedException $e) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode($e->errors));
        }
    }
}
