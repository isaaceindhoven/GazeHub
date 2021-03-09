<?php

declare(strict_types=1);

$data = json_decode(file_get_contents('php://input'), true);

require('Gaze.php');

$gaze = new Gaze();
$gaze->emit($data['topic'], $data['payload']);

return 'Done';
