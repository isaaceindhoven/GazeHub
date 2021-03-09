<?php

declare(strict_types=1);

namespace GazeHub\Models;

use Firebase\JWT\JWT;
use GazeHub\Services\ConfigRepository;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function call_user_func_array;
use function file_get_contents;
use function str_replace;

class Request
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var array
     */
    private $token;

    public function __construct(ConfigRepository $config)
    {
        $this->publicKey = file_get_contents($config->get('jwt_public_key'));
    }

    /**
     * @return any
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->originalRequest, $name], $arguments);
    }

    /**
     * @return any
     */
    public function __get(string $name)
    {
        return $this->originalRequest->$name;
    }

    public function isAuthorized(): bool
    {
        $authInHeader = $this->originalRequest->hasHeader('Authorization');
        $authInQuery = array_key_exists("token", $this->originalRequest->getQueryParams());

        $token = null;

        if (($authInHeader == FALSE) && ($authInQuery == FALSE)){
            return false;
        }

        if ($authInHeader){
            $token = str_replace('Bearer ', '', $this->originalRequest->getHeaderLine('Authorization'));
        }

        if ($authInQuery){
            $token =  $this->originalRequest->getQueryParams()["token"];
        }

        try {
            $this->token = JWT::decode($token, $this->publicKey, ['RS256']);
        } catch (Throwable $th) {
            return false;
        }

        return true;
    }

    public function setOriginalRequest(ServerRequestInterface $request): void
    {
        $this->originalRequest = $request;
    }

    public function getTokenPayload(): array
    {
        return (array) $this->token;
    }
}
