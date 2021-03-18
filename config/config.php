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

use GazeHub\Log;

return [
    'routes_config' => __DIR__ . '/routes.php',

    'server_port' => '3333',
    'server_host' => '0.0.0.0',

    'jwt_public_key' => __DIR__ . '/../public.key',
    'jwt_verify' => false,
    'jwt_alg' => 'RS256',

    'log_level' => Log::DEBUG,
];
