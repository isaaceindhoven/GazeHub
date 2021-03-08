<?php

namespace GazeHub\Controllers;

use GazeHub\Services\StreamRepository;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use Firebase\JWT\JWT;
use Throwable;

class EventController {

    private $streamRepository;
    private $publicKey;

    function __construct(StreamRepository $streamRepository) {
        $this->streamRepository = $streamRepository;
        $this->publicKey = file_get_contents(__DIR__ . "/../../public.key");
    }

    function handle(ServerRequestInterface $request){
        
        if ($request->hasHeader("Authorization") == FALSE) return new Response(401);
        
        $token = str_replace("Bearer ", "", $request->getHeaderLine("Authorization"));

        try {
            $decoded = JWT::decode($token, $this->publicKey, array('RS256'));
            if ($decoded->role != "server") return new Response(401);
            
            $data = (string) $request->getBody();
        
            $this->streamRepository->forEach(static function($stream) use ($data) {
                $stream->write($data);
            });

            return new Response(200, [ 'Content-Type' => 'text/html' ], 'Completed');

        } catch (Throwable $th) {
            return new Response(401);
        }
        
    }

}