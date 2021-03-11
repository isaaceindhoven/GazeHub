<?php

declare(strict_types=1);

namespace GazeHub\Models;

use Firebase\JWT\JWT;
use GazeHub\Exceptions\UnAuthorizedException;
use GazeHub\Services\ConfigRepository;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function array_key_exists;
use function call_user_func_array;
use function file_get_contents;
use function is_array;
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

    public function isAuthorized()
    {
        $token = $this->getHeaderLine('Authorization');

        $tokenInQuery = is_array($this->getQueryParams()) && array_key_exists('token', $this->getQueryParams());

        if ($token === null && $tokenInQuery) {
            $token = $this->getQueryParams['token'];
        } elseif ($token !== null) {
            $token = str_replace('Bearer ', '', $token);
        }

        try {
            $this->token = JWT::decode($token, $this->publicKey, ['RS256']);
        } catch (Throwable $th) {
            throw new UnAuthorizedException();
        }
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
