<?php

declare(strict_types=1);

namespace GazeHub;

use DI\Container;
use GazeHub\Middlewares\CorsMiddleware;
use React\EventLoop\Factory;
use React\Http\Server as HttpServer;
use React\Socket\Server;

class Hub
{
    /**
     * @var Container
     */
    private $container;

    public function setup()
    {
        $this->container = new Container();
    }

    public function run()
    {
        $loop = Factory::create();
        $socket = new Server('0.0.0.0:8000', $loop);

        $server = new HttpServer(
            $loop,
            [$this->container->get(CorsMiddleware::class), 'handle'],
            [$this->container->get(Router::class), 'route']
        );

        $server->listen($socket);

        echo 'Start HTTP server on port 8000' . "\n";

        $loop->run();
    }
}
