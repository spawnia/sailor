#!/usr/bin/env bash
set -euxo pipefail

rm -f sailor.php
composer update

if vendor/bin/sailor; then
  echo Expected the initial run of vendor/bin/sailor to exit with an error
  exit 1;
fi

if ! test -f sailor.php; then
  echo Expected sailor.php to be present after first executing vendor/bin/sailor
  exit 1
fi
