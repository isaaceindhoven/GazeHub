<?php

/**
 *   Do not remove or alter the notices in this preamble.
 *   This software code regards ISAAC Standard Software.
 *   Copyright © 2021 ISAAC and/or its affiliates.
 *   www.isaac.nl All rights reserved. License grant and user rights and obligations
 *   according to applicable license agreement. Please contact sales@isaac.nl for
 *   questions regarding license and user rights.
 */

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

    public function send(array $data)
    {
        $this->stream->write($data);
    }
}
