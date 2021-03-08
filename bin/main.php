<?php

require(__DIR__ . '/../vendor/autoload.php');

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('0.0.0.0:8000', $loop);

$streams = new SplObjectStorage();

$server = new React\Http\Server( $loop, function (ServerRequestInterface $request) use ($streams) {

    if ($request->getUri()->getPath() === '/sse') {
        $stream = new React\Stream\ThroughStream(function ($data) {
            return 'data: ' . $data . "\n\n";
        });

        $stream->on("close", function() use ($streams, $stream) {
            $streams->detach($stream);
        });

        $streams->attach($stream);

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $stream);
    }

    if ($request->getUri()->getPath() === '/send') {
        echo (count($streams) . "\n");
        foreach($streams as $stream){
            $stream->write(microtime(true) . " hello world");
        }
        return new Response(200, [ 'Content-Type' => 'text/html' ], "Send All Messages");
    }

    if ($request->getUri()->getPath() === '/') {
        return new Response(200, [ 'Content-Type' => 'text/html' ], file_get_contents(__DIR__ . "/../client.html"));
    }

    return new Response(404, ['Content-Type' => 'text/html'], "Not Found");

});

$server->listen($socket);

echo("Start TCP server http://localhost:8000\n");

$loop->run();