<?php

namespace GazeHub\Controllers;

use GazeHub\Services\StreamRepository;
use React\Stream\ThroughStream;
use React\Http\Message\Response;

class SSEController {

    private $streamRepository;

    function __construct(StreamRepository $streamRepository) {
        $this->streamRepository = $streamRepository;
    }

    function handle(){
        $stream = new ThroughStream(static function ($data) {
            return 'data: ' . $data . "\n\n";
        });

        $scope = $this;

        $stream->on('close', static function () use ($scope, $stream) {
            $scope->streamRepository->remove($stream);
        });

        $this->streamRepository->add($stream);

        return new Response(200, [ 'Content-Type' => 'text/event-stream' ], $stream);
    }

}