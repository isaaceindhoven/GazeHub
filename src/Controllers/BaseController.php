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

namespace GazeHub\Controllers;

use React\Http\Message\Response;

use function json_encode;

abstract class BaseController
{
    private function end(string $text, array $headers, int $statusCode): Response
    {
        return new Response($statusCode, $headers, $text);
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return $this->end(json_encode($data), [ 'Content-Type' => 'application/json' ], $statusCode);
    }

    protected function html(string $html, int $statusCode = 200): Response
    {
        return $this->end($html, [ 'Content-Type' => 'text/html' ], $statusCode);
    }
}
