<?php

declare(strict_types=1);

namespace GazeHub\Models;

use React\Stream\ThroughStream;

class Client
{
    /**
     * @var ThroughStream
     */
    public $stream;

    /**
     * @var string[]
     */
    public $roles;

    /**
     * @var string
     */
    public $tokenId;
}
