#!/usr/bin/env bash
set -euxo pipefail

composer update
vendor/bin/sailor

foo=`php queryFoo.php`
if [ $foo != "bar" ]; then
  echo The result of executing queryFoo.php was not bar: $foo
  exit 1
fi
