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
use Exception;
use GazeHub\Middlewares\CorsMiddleware;
use GazeHub\Middlewares\JsonParserMiddleware;
use GazeHub\Services\ConfigRepository;
use React\EventLoop\Factory;
use React\Http\Server as HttpServer;
use React\Socket\Server;

use function sprintf;

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
        $config = $this->container->get(ConfigRepository::class);

        $host = $config->get('server_host');
        $port = $config->get('server_port');

        $loop = Factory::create();
        $socket = new Server(sprintf('%s:%s', $host, $port), $loop);
        Log::setLogLevel($loop, $config->get('log_level'));

        $server = new HttpServer(
            $loop,
            [$this->container->get(CorsMiddleware::class), 'handle'],
            [$this->container->get(JsonParserMiddleware::class), 'handle'],
            [$this->container->get(Router::class), 'route']
        );

        $server->on('error', [$this, 'onError']);

        $server->listen($socket);

        LOG::info(sprintf('Server running on %s:%s', $host, $port));

        $loop->run();
    }

    public function onError(Exception $e)
    {
        Log::error($e->getMessage());
        if ($e->getPrevious() !== null && $e->getPrevious()->getMessage() !== '') {
            Log::error($e->getPrevious()->getMessage());
        }
    }
}
