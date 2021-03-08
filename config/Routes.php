<?php

declare(strict_types=1);

use GazeHub\Controllers\EventController;
use GazeHub\Controllers\SSEController;
use GazeHub\Controllers\SubscriptionController;

return [
    'GET' => [
        '/sse' => [SSEController::class, 'handle'],
    ],
    'POST' => [
        '/event' => [EventController::class, 'handle'],
        '/subscription' => [SubscriptionController::class, 'create'],
    ],
    'DELETE' => [
        '/subscription' => [SubscriptionController::class, 'destroy'],
    ],
];
