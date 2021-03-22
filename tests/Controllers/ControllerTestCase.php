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

use DI\Container;
use GazeHub\Router;
use GazeHub\Services\ClientRepository;
use GazeHub\Services\ConfigRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use React\Http\Message\Response;

use function base64_encode;
use function explode;
use function json_encode;
use function parse_url;
use function uniqid;

use const PHP_URL_PATH;
use const PHP_URL_QUERY;

// phpcs:ignore ObjectCalisthenics.Metrics.MethodPerClassLimit.ObjectCalisthenics\Sniffs\Metrics\MethodPerClassLimitSniff
class ControllerTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string;
     */
    private $method;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var array
     */
    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.WriteOnlyProperty
    private $headers;

    /**
     * @var array
     */
    private $body;

    public function __construct()
    {
        parent::__construct();
        $this->container = new Container();
    }

    protected function setUp(): void
    {
        $this->response = null;
        $this->headers = null;
        $this->body = null;
    }

    protected function req(string $url, string $method): self
    {
        $this->setUp();
        $this->url = $url;
        $this->method = $method;
        return $this;
    }

    protected function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    private function generateToken(array $payload): string
    {
        return 'KEEPME.' . base64_encode(json_encode($payload)) . '.KEEPME';
    }

    protected function asServer(): self
    {
        $this->setHeaders(['Authorization' => 'Bearer ' . $this->generateToken(['role' => 'server'])]);
        return $this;
    }

    protected function registerClient(string $jti): self
    {
        $clientRepo = $this->container->get(ClientRepository::class);
        $clientRepo->add([], $jti);
        return $this;
    }

    protected function asClient(string $jti = null): self
    {
        $this->setHeaders(['Authorization' => 'Bearer ' . $this->getClientToken($jti) ]);
        return $this;
    }

    protected function getClientToken(string $jti = null): string
    {
        if ($jti === null) {
            $jti = uniqid();
        }
        return $this->generateToken(['roles' => [], 'jti' => $jti]);
    }

    protected function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    protected function do(): self
    {
        $configRepo = new ConfigRepository();
        $configRepo->loadConfig(__DIR__ . '/../assets/testConfig.php');

        $this->container->set(ConfigRepository::class, $configRepo);

        $router = new Router($this->container);
        $this->response = $router->route($this->buildOriginalRequest());

        return $this;
    }

    private function buildOriginalRequest(): MockObject
    {
        $originalRequest = $this->createMock(ServerRequestInterface::class);

        $originalRequest->method('getMethod')->willReturn($this->method);
        $originalRequest->method('getParsedBody')->willReturn($this->body);
        $originalRequest->method('getQueryParams')->willReturn($this->getParsedQuery());
        $scope = $this;
        $originalRequest
            ->method('getHeaderLine')
            ->will($this->returnCallback(static function ($key) use ($scope) {
                return $scope->headers[$key];
            }));

        $uriMock = $this->createMock(UriInterface::class);
        $uriMock->method('getPath')->willReturn(parse_url($this->url, PHP_URL_PATH));

        $originalRequest->method('getUri')->willReturn($uriMock);

        return $originalRequest;
    }

    protected function assertHttpCode(int $code)
    {
        if ($this->response === null) {
            $this->do();
        }

        $this->assertEquals($code, $this->response->getStatusCode());
    }

    private function getParsedQuery(): array
    {
        if (!parse_url($this->url, PHP_URL_QUERY)) {
            return [];
        }
        $params = explode('&', parse_url($this->url, PHP_URL_QUERY));
        $queryParams = [];
        foreach ($params as $param) {
            [$key, $val] = explode('=', $param);
            $queryParams[$key] = $val;
        }
        return $queryParams;
    }
}
