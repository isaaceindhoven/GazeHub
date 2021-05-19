# GazeHub
[GazeHub](https://github.com/isaaceindhoven/GazeHub) is a server that is responsible for sending data from the backend to the frontend. <br/>See the [Complete install](complete-install.md) page for a detailed guide of how to install Gaze.

### Configure

<!-- tabs:start -->

#### **Using a gazehub.config.json file (recommended)**

GazeHub will try to load a `gazehub.config.json` from the current working directory. Use the `-c` argument to specify a different location. Example: `./vendor/bin/gazehub -c='settings/gaze.config.json'`

```json
// gazehub.config.json
{
    "port": 3333,
    "host": "0.0.0.0"
}
```


#### **Using environment variables**

You can also override the environment variables. For example, the default host and port are `0.0.0.0:3333` to modify this you can run `GAZEHUB_HOST=192.168.178.10 GAZEHUB_PORT=8005 ./vendor/bin/gazehub`.

<!-- tabs:end -->

### Available settings
Run `./vendor/bin/gazehub -h` to view the default settings.

|JSON Key|Environment Name|Default Value|Description|
|---|---|---|---|
|port|GAZEHUB_PORT|`3333`|The port that GazeHub runs on.|
|host|GAZEHUB_HOST|`'0.0.0.0'`|The IP that GazeHub runs on.|
|jwt_public_key|GAZEHUB_JWT_PUBLIC_KEY|`''`|Content of the public key|
|jwt_alg|GAZEHUB_JWT_ALG|`'RS256'`|The signing algorithm used for the JWT tokens|
|log_level|GAZEHUB_LOG_LEVEL|`'INFO'`| The level at which the logger will output a value. Available options are: `'DEBUG'`, `'INFO'`, `'WARN'` and `'ERROR'`|

### Running GazeHub

<!-- tabs:start -->

#### **CLI**

```bash
GAZEHUB_JWT_PUBLIC_KEY=$(cat public.key) ./vendor/bin/gazehub
```

#### **Docksal**

Docksal is supported out of the box. You can simply run the `fin up` command.
If you want to override the default settings we highly recommend to chose the `gazehub.config.json` method.

#### **Docker Compose**

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

#### **Supervisor**

Place a configuration file for Supervisor in `/etc/supervisor/conf.d/gazehub.conf` to let Supervisor manage the GazeHub process.

```ini
[program:gazehub]
command = <PROJECT ROOT>/vendor/bin/gazehub
stdout_logfile = /var/log/supervisor/gazehub-stdout
stderr_logfile = /var/log/supervisor/gazehub-stderr
```

<!-- tabs:end -->

### Development

| Command | Description |
| ------- | ----------- |
| `./vendor/bin/phpunit` | Runs [PHPUnit](https://phpunit.de/) tests that are present in `/tests/`. |
| `./vendor/bin/phpunit --coverage-html coverage` | Runs the [PHPUnit](https://phpunit.de/) tests and outputs the code coverage to `coverage/index.html`. |
| `./vendor/bin/phpstan` | Runs [PHPStan](https://github.com/phpstan/phpstan) Analysis Tool. |
| `./vendor/bin/phpcs` | Runs [PHP Codesniffer](https://github.com/squizlabs/PHP_CodeSniffer). |
| `./vendor/bin/phpcbf` | Runs code beautifier. |

### Contributing

The main code of GazeHub is hosted [here](https://github.com/isaaceindhoven/GazeHub-src).
