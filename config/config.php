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
use GazeHub\Services\Environment;

return [
    'routes_config' => __DIR__ . '/routes.php',

    'server_port' => Environment::get('GAZEHUB_SERVER_HOST', '3333'),
    'server_host' => Environment::get('GAZEHUB_SERVER_PORT', '0.0.0.0'),

    'jwt_public_key' => Environment::get('GAZEHUB_JWT_PUBLIC_KEY', __DIR__ . '/../public.key'),
    'jwt_verify' => (bool) Environment::get('GAZEHUB_JWT_VERIFY', true),
    'jwt_alg' => Environment::get('GAZEHUB_JWT_ALG', 'RS256'),

    'log_level' => (int) Environment::get('GAZEHUB_LOG_LEVEL', Log::INFO),
];
