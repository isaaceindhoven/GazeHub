#!/usr/bin/env bash

GREEN="\033[0;32m"
RED="\033[0;31m"
CYAN="\033[0;36m"
NO_COLOR="\033[0m"

function success() {
  echo -e "${GREEN}$1${NO_COLOR}"
}

function info() {
  echo -e "${CYAN}$1${NO_COLOR}"
}

function error() {
  echo -e "${RED}$1${NO_COLOR}"
}

function runTest() {
  PHP=$1

  info "Running tests with PHP $PHP"

  docker run --rm -v "$PWD":/app -w /app php:"$PHP"-cli ./vendor/bin/phpunit

  if [[ -$? -ne 0 ]]; then
    error "PHP $PHP Failed!, see phpunit output above️"
    exit 1
  else
    success "PHP $PHP succeeded️"
  fi
}

versions=('7.3' '7.4' '8.0')

for version in "${versions[@]}"; do
  runTest "$version"
done
