#!/usr/bin/env sh
#   Do not remove or alter the notices in this preamble.
#   This software code regards ISAAC Standard Software.
#   Copyright © 2021 ISAAC and/or its affiliates.
#   www.isaac.nl All rights reserved. License grant and user rights and obligations
#   according to applicable license agreement. Please contact sales@isaac.nl for
#   questions regarding license and user rights.

rm -rf gazehub-src
git clone git@gitlab.isaac.local:study/php-chapter/real-time-ui-updates/gazehub-src.git
cd gazehub-src || exit 1
composer install --no-dev
php --define phar.readonly=0 create-archive.php
mv gazehub.phar ../gazehub.phar
cd ..
rm -rf gazehub-src
git add .
