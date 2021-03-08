<?php

namespace GazeHub\Controllers;

use GazeHub\Services\StreamRepository;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

class EventController {

    private $streamRepository;

    function __construct(StreamRepository $streamRepository) {
        $this->streamRepository = $streamRepository;
    }

    function handle(ServerRequestInterface $request){

        // TODO: check if header JWT token is valid

        $data = (string) $request->getBody();
        
        $this->streamRepository->forEach(static function($stream) use ($data) {
            $stream->write($data);
        });

        return new Response(200, [ 'Content-Type' => 'text/html' ], 'Send All Messages');
    }

}