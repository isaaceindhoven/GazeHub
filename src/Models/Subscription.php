<?php

declare(strict_types=1);

namespace GazeHub\Models;

class Subscription
{
    /**
     * @var Client
     */
    public $client;

    /**
     * @var string
     */
    public $callbackId;

    /**
     * @var string
     */
    public $topic;
}
