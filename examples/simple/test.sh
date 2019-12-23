#!/usr/bin/env bash
set -euxo pipefail

composer update
vendor/bin/sailor

result=`php src/test.php`
if [ $result != "bar" ]; then
  echo The result of executing src/test.php was not "bar", got: $result
  exit 1
fi
