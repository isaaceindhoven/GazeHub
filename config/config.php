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

return [
    'server_port' => '8000',
    'server_host' => '0.0.0.0',
    'jwt_public_key' => __DIR__ . '/../public.key',
    'routes_config' => __DIR__ . '/routes.php',
];
