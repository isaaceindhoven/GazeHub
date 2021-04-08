#!/usr/bin/env sh

# 1. Clone gazehub-src
# 2. Cd into cloned folder
# 3. Composer install
# 4. Build PHAR
# 5. Move PHAR to root
# 6. Remove clone repo
# 7. Add git commit
# 8. Push

rm -rf gazehub-src
git clone git@gitlab.isaac.local:study/php-chapter/real-time-ui-updates/gazehub-src.git
cd gazehub-src
composer install --no-dev
php --define phar.readonly=0 create-archive.php
mv gazehub.phar ../gazehub.phar
cd ..
rm -rf gazehub-src
git add .
git commit -am "Released new Gazehub"
git push
