<?php

declare(strict_types=1);

require('Gaze.php');

$gaze = new Gaze();
$gaze->emit('ProductCreated/1', ['id' => 1, 'name' => 'Shirt']);

return 'Done';
