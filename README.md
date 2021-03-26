# GazeHub

## Run as standalone project
```shell script
./gazehub.phar
```

## Run as composer dependency
```shell script
./vendor/bin/gazehub.phar
```

## Build new release
```shell script
./release.sh
```

This script will do the following steps:
1. Clone [gazehub-src](https://gitlab.isaac.nl/study/php-chapter/real-time-ui-updates/gazehub-src) into `gazehub`.
1. Run `composer install`.
1. Create a new `gazehub.phar` and move it to the right place.
1. Remove the cloned `gazehub` folder.
1. Create a new git commit and push that commit.
