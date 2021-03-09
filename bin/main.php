<?php

declare(strict_types=1);

use GazeHub\Hub;

require(__DIR__ . '/../vendor/autoload.php');

$hub = new Hub();

$hub->setup();
$hub->run();
