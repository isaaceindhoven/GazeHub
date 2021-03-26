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

// Load composer autoload
$composerFile = null;

$libraryAutoload = __DIR__ . '/../../../autoload.php';
$projectAutoload = __DIR__ . '/../vendor/autoload.php';

foreach ([$libraryAutoload, $projectAutoload] as $file) {
    if (file_exists($file)) {
        $composerFile = $file;
        break;
    }
}

if ($composerFile === null) {
    fwrite(STDERR, 'You can use gazehub as dependency in a project, ' .
        'or as a standalone application, but make sure you install the dependencies using composer.');

    die(1);
}

require $composerFile;
require __DIR__ . '/main.php';
