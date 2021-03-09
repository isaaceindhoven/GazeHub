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

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $operator;

    /**
     * @var string
     */
    public $value;
}
