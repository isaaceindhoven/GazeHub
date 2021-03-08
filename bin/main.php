<?php

declare(strict_types=1);
require(__DIR__ . '/../vendor/autoload.php');

use DI\Container;
use GazeHub\Middlewares\CorsMiddleware;
use GazeHub\Router;

$container = new Container();
$router = $container->get(Router::class);

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('0.0.0.0:8000', $loop);

$server = new React\Http\Server($loop, [new CorsMiddleware(), 'handle'], [$router, 'route']);

$server->listen($socket);

echo("Start TCP server http://localhost:8000\n");

$loop->run();
