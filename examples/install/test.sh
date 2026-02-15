#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 0 ]]; then
  echo "Usage: examples/install/test.sh" >&2
  exit 1
fi

echo "Clean up previous runs"
rm -f sailor.php

if first_output="$(vendor/bin/sailor 2>&1)"; then
  echo "Expected the initial run of vendor/bin/sailor to exit with an error" >&2
  exit 1
fi

if [[ "$first_output" != *"configuration file sailor.php does not exist"* ]]; then
  echo "Expected first run to fail because sailor.php is missing" >&2
  exit 1
fi

if [[ ! -f "sailor.php" ]]; then
  echo "Expected sailor.php to be present after first executing vendor/bin/sailor" >&2
  exit 1
fi

if ! second_output="$(vendor/bin/sailor 2>&1)"; then
  if [[ "$second_output" == *"configuration file sailor.php does not exist"* ]]; then
    echo "Expected second run to not fail because sailor.php is missing" >&2
    exit 1
  fi
fi
