<?php

declare(strict_types=1);

namespace GazeHub\Models;

class Subscription
{
    public $client;

    public $callbackId;
    
    public $topic;
    
    public $field;
    
    public $operator;
    
    public $value;
}
