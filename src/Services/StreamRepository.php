<?php

namespace GazeHub\Services;

use Iterator;
use SplObjectStorage;

class StreamRepository {

    private $streams;

    function __construct() {
        $this->streams = new SplObjectStorage();
    }

    public function add($stream){
        echo("Adding Client");
        $this->streams->attach($stream);
    }

    public function remove($stream){
        echo("Removing Client");
        $this->streams->detach($stream);
    }

    public function forEach($callback){
        foreach($this->streams as $stream){
            $callback($stream);
        }
    }
}