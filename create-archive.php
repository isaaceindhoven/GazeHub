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

/*
 * Command for generating archive (PHAR):
 * php --define phar.readonly=0 create-archive.php
 */

try {
    $pharFile = 'gazehub.phar';

    if (file_exists($pharFile)) {
        unlink($pharFile);
    }

    $phar = new Phar($pharFile);

    $phar->startBuffering();

    $stub = $phar->createDefaultStub('bin/autoload.php');
    $stub = "#!/usr/bin/env php \n" . $stub;

    $phar->buildFromDirectory(__DIR__);
    $phar->setStub($stub);
    $phar->stopBuffering();
    $phar->compressFiles(Phar::GZ);

    chmod(__DIR__ . '/' . $pharFile, 0770);

    echo sprintf("%s successfully created\n", $pharFile);
} catch (Exception $e) {
    echo $e->getMessage();
}
