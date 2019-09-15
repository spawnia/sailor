#!/usr/bin/env bash
set -euxo pipefail

composer update
vendor/bin/sailor

if ! test -f sailor.php; then
  echo Expected sailor.php to be present after first executing vendor/bin/sailor
  exit 1
fi
