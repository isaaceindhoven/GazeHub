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

namespace GazeHub\Tests\Controllers;

class SSEControllerTest extends ControllerTestCase
{
    public function testReponse401IfUnauthorized()
    {
        $this->req('/sse', 'GET')->assertHttpCode(401);
    }

    public function testReponse200IfAuthorized()
    {
        $this->req('/sse?token=' . $this->getClientToken(), 'GET')->assertHttpCode(200);
    }
}
