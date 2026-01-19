#!/usr/bin/env bash
set -euxo pipefail

composer update
composer reinstall spawnia/sailor
vendor/bin/sailor

php src/test.php
