#!/usr/bin/env bash

function runTest {
  PHP=$1

  echo "Running tests with PHP $PHP"

  output=$(docker run --rm -v "$PWD":/app -w /app php:"$PHP"-cli ./vendor/bin/phpunit)

  if [[ -$? -ne 0 ]]; then
    echo "$output"
    echo "\033[0;31m⚠️ PHP $PHP Failed!, see phpunit output above ⚠️\033[0m"
    exit 1
  else
    echo "PHP $PHP succeeded"
  fi
}

versions=('7.3' '7.4' '8.0')

for version in "${versions[@]}"; do
  runTest "$version"
done
