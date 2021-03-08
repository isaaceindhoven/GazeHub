<?php

declare(strict_types=1);

namespace GazeHub\Services;

use SplObjectStorage;

class StreamRepository
{
    /**
     * @var SplObjectStorage
     */
    private $streams;

    public function __construct()
    {
        $this->streams = new SplObjectStorage();
    }

    /**
     * @param  mixed $stream
     * @return void
     */
    public function add($stream)
    {
        $this->streams->attach($stream);
    }

    /**
     * remove
     *
     * @param  mixed $stream
     * @return void
     */
    public function remove($stream)
    {
        $this->streams->detach($stream);
    }

    /**
     * forEach
     *
     * @param  Callable $callback
     * @return void
     */
    public function forEach($callback)
    {
        foreach ($this->streams as $stream) {
            $callback($stream);
        }
    }
}
