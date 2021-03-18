<?php

declare(strict_types=1);

try {
    $pharFile = 'gazehub.phar';

    // clean up
    if (file_exists($pharFile)) {
        unlink($pharFile);
    }

    if (file_exists($pharFile . '.gz')) {
        unlink($pharFile . '.gz');
    }

    // create phar
    $phar = new Phar($pharFile);

    // start buffering. Mandatory to modify stub to add shebang
    $phar->startBuffering();

    // Create the default stub from main.php entrypoint
    $stub = $phar->createDefaultStub('gazehub');

    // Add the rest of the apps files
    $phar->buildFromDirectory(__DIR__);

    // Add the stub
    $phar->setStub($stub);

    $phar->stopBuffering();

    // plus - compressing it into gzip
    $phar->compressFiles(Phar::GZ);

    # Make the file executable
    chmod(__DIR__ . '/' . $pharFile, 0770);

    echo sprintf("%s successfully created\n", $pharFile);
} catch (Exception $e) {
    echo $e->getMessage();
}
