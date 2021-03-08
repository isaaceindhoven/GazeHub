<?php

namespace GazeHub\Services;

use SplObjectStorage;

class StreamRepository {

    private $streams;

    function __construct() {
        $this->streams = new SplObjectStorage();
    }

    public function add($stream){
        $this->streams->attach($stream);
    }

    public function remove($stream){
        $this->streams->detach($stream);
    }

    public function forEach($callback){
        foreach($this->streams as $stream){
            $callback($stream);
        }
    }
}