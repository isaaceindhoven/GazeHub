<?php

namespace GazeHub\Controllers;

use GazeHub\Services\StreamRepository;
use React\Http\Message\Response;

class EventController {

    private $streamRepository;

    function __construct(StreamRepository $streamRepository) {
        $this->streamRepository = $streamRepository;
    }

    function handle(){
        $this->streamRepository->forEach(static function($stream){
            $stream->write('hello world');
        });

        return new Response(200, [ 'Content-Type' => 'text/html' ], 'Send All Messages');
    }

}