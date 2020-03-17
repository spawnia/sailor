#!/usr/bin/env bash
set -euxo pipefail

composer update
vendor/bin/sailor

php src/test.php
