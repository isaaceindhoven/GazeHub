<?php

use GazeHub\Controllers\EventController;
use GazeHub\Controllers\SSEController;

return [
    "GET" => [
        "/sse" => [SSEController::class, 'handle'],
    ],
    "POST" => [
        "/event" => [EventController::class, 'handle'],
    ]
];
