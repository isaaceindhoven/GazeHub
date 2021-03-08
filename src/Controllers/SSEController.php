<?php

declare(strict_types=1);

namespace GazeHub\Controllers;

use GazeHub\Services\StreamRepository;
use React\Http\Message\Response;
use React\Stream\ThroughStream;

class SSEController
{
    /**
     * @var StreamRepository
     */
    private $streamRepository;

    public function __construct(StreamRepository $streamRepository)
    {
        $this->streamRepository = $streamRepository;
    }

    /**
     * @return Response
     */
    public function handle()
    {
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
