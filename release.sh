#!/usr/bin/env sh

rm -rf gazehub-src
git clone git@github.com:isaaceindhoven/GazeHub-src.git gazehub-src
cd gazehub-src || exit 1
composer install --no-dev
php --define phar.readonly=0 create-archive.php
mv gazehub.phar ../gazehub.phar
cd ..
rm -rf gazehub-src
git add .
