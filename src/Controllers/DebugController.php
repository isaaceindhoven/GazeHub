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

use GazeHub\Services\ConfigRepository;
use React\Http\Message\Response;

use function file_get_contents;

class DebugController extends BaseController
{
    /**
     * @var bool $debugEnabled
     */
    private $debugEnabled;

    public function __construct(ConfigRepository $configRepository)
    {
        $this->debugEnabled = $configRepository->get('jwt_verify') === false;
    }

    public function handle(): Response
    {
        if (!$this->debugEnabled) {
            return new Response(404);
        }

        $debugHtml = file_get_contents(__DIR__ . '/../../public/debug.html');
        return new Response(200, ['Content-Type' => 'text/html'], $debugHtml);
    }
}
