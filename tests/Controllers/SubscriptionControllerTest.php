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

class SubscriptionControllerTest extends ControllerTestCase
{
    public function testSubscibeResponse401IfUnauthorized()
    {
        $this->req('/subscription', 'POST')->assertHttpCode(401);
    }

    public function testUnsubscibeResponse401IfUnauthorized()
    {
        $this->req('/subscription', 'DELETE')->assertHttpCode(401);
    }

    public function testSubscribeResponse200IfAuthorized()
    {
        $this
            ->req('/subscription', 'POST')
            ->registerClient('client1')
            ->asClient('client1')
            ->setBody(['callbackId' => 'abc', 'topics' => ['ProductCreated']])
            ->assertHttpCode(200);
    }

    public function testUnsubscribeResponse200IfAuthorized()
    {
        $this
            ->req('/subscription', 'DELETE')
            ->registerClient('client1')
            ->asClient('client1')
            ->setBody(['topics' => ['ProductCreated']])
            ->assertHttpCode(200);
    }

    public function testSubscribeResponse401IfClientNotRegistered()
    {
        $this
            ->req('/subscription', 'POST')
            ->asClient('client1')
            ->setBody(['callbackId' => 'abc', 'topics' => ['ProductCreated']])
            ->assertHttpCode(401);
    }

    public function testUnsubscribeResponse401IfClientNotRegistered()
    {
        $this
            ->req('/subscription', 'DELETE')
            ->asClient('client1')
            ->setBody(['topics' => ['ProductCreated']])
            ->assertHttpCode(401);
    }
}
