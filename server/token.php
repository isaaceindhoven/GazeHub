<?php

declare(strict_types=1);

require('Gaze.php');

$gaze = new Gaze();
echo json_encode(['token' => $gaze->generateClientToken(['admin']) ]);
