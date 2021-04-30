# GazeHub
GazeHub is a server that is responsible for sending from the backend to the frontend.

## Installation
GazeHub can be installed using [Composer](https://getcomposer.org/) with the following command:

```bash
composer require isaac/gaze-hub
```

## Keypairs
!> We highly recommend you read the [Authentication](authentication) page.

If you have not generated a public and private key pair you can do so with these 2 commands:

```bash
# Generate private key
openssl genrsa -out private.key 4096

# Extract public key from private key
openssl rsa -in private.key -outform PEM -pubout -out public.key
```

## Easy configuration
1. Create a `gazehub.config.json` file in your project root with the following contents.
```json
{
    "port": 3333,
    "host": "0.0.0.0"
}
```
1. You can run `GAZEHUB_JWT_PUBLIC_KEY=$(cat public.key) ./vendor/bin/gazehub` to start GazeHub.


## Advanced configuration

### Environment variables override
You can also override the environment variables. For example, the default host and port are `0.0.0.0:3333` to modify this you can run `GAZEHUB_HOST=192.168.178.10 GAZEHUB_PORT=8005 ./vendor/bin/gazehub`.

### All available settings
Run `./vendor/bin/gazehub -h` to view the default config.json contents.

|JSON Key|Environment Name|Default Value|Description|
|---|---|---|---|
|port|GAZEHUB_PORT|`3333`|The port that GazeHub runs on.|
|host|GAZEHUB_HOST|`'0.0.0.0'`|The IP that GazeHub runs on.|
|jwt_public_key|GAZEHUB_JWT_PUBLIC_KEY|`''`|Content of the public key|
|jwt_alg|GAZEHUB_JWT_ALG|`'RS256'`|The signing algorithm used for the JWT tokens|
|log_level|GAZEHUB_LOG_LEVEL|`'INFO'`| The level at which the logger will output a value. Available options are: `'DEBUG'`, `'INFO'`, `'WARN'` and `'ERROR'`|

### Change `gazehub.config.json` location
GazeHub will automatically load the `gazehub.config.json` from the current working directory. Use the `-c` argument to specify a different location. Example: `./vendor/bin/gazehub -c='settings/gaze.config.json'`

### Logging
To save all the output to a log file you can run: `./vendor/bin/gazehub > log.txt`.

## Usage

### Directly from the CLI

```bash
./vendor/bin/gazehub
```

### [Docksal](https://docksal.io/)

Docksal is supported out of the box. You can simply run the command.
If you want to override the default settings we highly recommend to chose the `gazehub.config.json` method.

### [Docker Compose](https://docs.docker.com/compose/)

You can add the following code to your `docker-compose.yml` file if you want to run it in Docker.

```yml
services:
    hub:
        image: php:7.3-cli
        ports:
            - 3333:3333
        volumes:
            - ./vendor/:/vendor
            - ./public.key:/public.key
            - ./gazehub.config.json:/gazehub.config.json
        environment:
            - GAZEHUB_JWT_PUBLIC_KEY=$(cat /public.key)
        working_dir: '/'
        command: '/vendor/bin/gazehub'
```

### Supervisor

Place a configuration file for Supervisor in `/etc/supervisor/conf.d/gazehub.conf` to let Supervisor manage the GazeHub process.

```ini
[program:gazehub]
command = <PROJECT ROOT>/vendor/bin/gazehub
stdout_logfile = /var/log/supervisor/gazehub-stdout
stderr_logfile = /var/log/supervisor/gazehub-stderr
```

## Development

| Command | Description |
| ------- | ----------- |
| `./vendor/bin/phpunit` | Runs [PHPUnit](https://phpunit.de/) tests that are present in `/tests/`. |
| `./vendor/bin/phpunit --coverage-html coverage` | Runs the [PHPUnit](https://phpunit.de/) tests and outputs the code coverage to `coverage/index.html`. |
| `./vendor/bin/phpstan` | Runs [PHPStan](https://github.com/phpstan/phpstan) Analysis Tool. |
| `./vendor/bin/phpcs` | Runs [PHP Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer). |
| `./vendor/bin/phpcbf` | Runs code beautifier. |

### Contributing

The main code of GazeHub is hosted [here](https://github.com/isaaceindhoven/GazeHub-src).
