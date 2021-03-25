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

namespace GazeHub\Models;

use GazeHub\Exceptions\DataValidationFailedException;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Services\JWTDecoder;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;
use Throwable;

use function array_key_exists;
use function is_array;
use function str_replace;

class Request
{
    /**
     * @var array
     */
    private $token;

    /**
     * @var ServerRequestInterface;
     */
    private $originalRequest;

    /**
     * @var JWTDecoder;
     */
    private $jwtDecoder;

    public function __construct(JWTDecoder $jwtDecoder)
    {
        $this->jwtDecoder = $jwtDecoder;
    }

    public function setOriginalRequest(ServerRequestInterface $request): void
    {
        $this->originalRequest = $request;
    }

    public function isAuthorized()
    {
        $token = $this->getHeaderValueByKey('Authorization');

        if ($token === null) {
            $token = $this->getValueByKey($this->originalRequest->getQueryParams(), 'token');
        }

        if ($token === null) {
            throw new UnAuthorizedException();
        }

        $token = str_replace('Bearer ', '', $token);

        try {
            $this->token = $this->jwtDecoder->decode($token);
        } catch (Throwable $th) {
            throw new UnAuthorizedException();
        }
    }

    public function isRole(string $role)
    {
        $this->isAuthorized();

        if ($this->getTokenPayload()['role'] !== $role) {
            throw new UnAuthorizedException();
        }
    }

    public function getBody(): array
    {
        return $this->originalRequest->getParsedBody();
    }

    public function getTokenPayload(): array
    {
        return (array) $this->token;
    }

    public function validate(array $checks): array
    {
        $validator = new Validator();

        $validation = $validator->validate($this->getBody(), $checks);

        if ($validation->fails()) {
            throw new DataValidationFailedException($validation->errors()->toArray());
        }

        return $validation->getValidData();
    }

    /**
     * @param null|array $arr
     * @return null|boolean
     */
    private function getValueByKey($arr, string $key)
    {
        if (is_array($arr) && array_key_exists($key, $arr)) {
            return $arr[$key];
        }
        return null;
    }

    /**
     * @return null|string
     */
    private function getHeaderValueByKey(string $key)
    {
        $value = $this->originalRequest->getHeaderLine($key);
        if ($value === null || $value === '') {
            return null;
        }
        return $value;
    }
}
