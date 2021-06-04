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
            - ./gazehub.config.json:/gazehub.config.json
        working_dir: '/'
        command: './vendor/bin/gazehub'
```

The `gazehub.config.json` needs to have the `jwt_public_key` option filled in with the contents of your public.key file. For example:

```json
{
    "port": 3333,
    "host": "0.0.0.0",
    "jwt_public_key" : "-----BEGIN PUBLIC KEY-----\nMIICIjAN..."
}
```

#### **Supervisor**

Place a configuration file for Supervisor in `/etc/supervisor/conf.d/gazehub.conf` to let Supervisor manage the GazeHub process.

```ini
[program:gazehub]
command = <PROJECT ROOT>/vendor/bin/gazehub
stdout_logfile = /var/log/supervisor/gazehub-stdout
stderr_logfile = /var/log/supervisor/gazehub-stderr
```

The `gazehub.config.json` needs to have the `jwt_public_key` option filled in with the contents of your public.key file. For example:

```json
{
    "port": 3333,
    "host": "0.0.0.0",
    "jwt_public_key" : "-----BEGIN PUBLIC KEY-----\nMIICIjAN..."
}
```

#### **Docksal**

1. Add config file for supervisor to project at `.docksal/services/cli/supervisor/gazehub.conf` (content below)
1. Add content at the end of `.docksal/docksal.yml` (content below)
1. Add config file for GazeHub to project at `gazehub.conf.json` (content below)
1. Add config override for apache at `.docksal/etc/apache/httpd-vhosts.conf` (content below)
1. Start project with `GAZEHUB_JWT_PUBLIC_KEY=$(cat public.key) fin up`

Content of `.docksal/services/cli/supervisor/gazehub.conf`:
```ini
[program:gazehub]
command = /var/www/vendor/bin/gazehub -c /var/www/gazehub.conf.json
stdout_logfile = /var/log/supervisor/gazehub-stdout
stderr_logfile = /var/log/supervisor/gazehub-stderr
```

Addition to `.docksal/docksal.yml`:
```yml
services:
  cli:
    volumes:
      - ./services/cli/supervisor/gazehub.conf:/etc/supervisor/conf.d/gazehub.conf
    environment:
      - GAZEHUB_JWT_PUBLIC_KEY
```

Content of `gazehub.conf.json`:
```json
{
    "port": 3333,
    "host": "0.0.0.0"
}
```

Content of `.docksal/etc/apache/httpd-vhosts.conf`:
```apacheconf
<VirtualHost *:80>
    ServerName ${APACHE_SERVERNAME}
    ServerAlias gazehub.*
    ProxyPass / http://cli:3333/ connectiontimeout=86400 timeout=86400
    ProxyPassReverse / http://cli:3333/
</VirtualHost>
```

#### **systemd**

Create a `.service` file for systemd in `/etc/systemd/system/` with the following contents:

```ini
[Unit]
Description=GazeHub

[Service]
User=<YOUR_USER>
WorkingDirectory=<ABSOLUTE_PATH_TO_YOUR_PROJECT>
ExecStart=<ABSOLUTE_PATH_TO_YOUR_PROJECT>/vendor/bin/gazehub
Restart=always

[Install]
WantedBy=multi-user.target
```

The `gazehub.config.json` needs to have the `jwt_public_key` option filled in with the contents of your public.key file. For example:

```json
{
    "port": 3333,
    "host": "0.0.0.0",
    "jwt_public_key" : "-----BEGIN PUBLIC KEY-----\nMIICIjAN..."
}
```

<!-- tabs:end -->
