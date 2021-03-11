#!/bin/bash

set -euo pipefail

function git_hooks::info() {
    echo -e "\033[0;32m$1\033[0m"
}

function git_hooks::warning() {
    echo -e "\033[0;31m$1\033[0m" >&2
}

function docksal::is_installed() {
  command -v fin &>/dev/null
  return $?
}

function docksal::is_running() {
  fin exec : &>/dev/null
  return $?
}

function docksal::custom_command_exists() {
  local commandName=$1
  [ -f "${PWD}/.docksal/commands/${commandName}" ]
}