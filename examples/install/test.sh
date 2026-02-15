#!/usr/bin/env bash
set -euo pipefail

if [[ $# -ne 0 ]]; then
  echo "Usage: examples/install/test.sh" >&2
  exit 1
fi

echo "Clean up previous runs"
rm -rf src sailor.php

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

if second_output="$(vendor/bin/sailor 2>&1)"; then
  echo "Expected second run of vendor/bin/sailor to fail in an empty project" >&2
  exit 1
fi

if [[ "$second_output" != *"Directory does not exist"* ]]; then
  echo "Expected second run to fail because src directory is missing" >&2
  echo "$second_output" >&2
  exit 1
fi

mkdir src

if ! third_output="$(vendor/bin/sailor 2>&1)"; then
  echo "Expected third run of vendor/bin/sailor to succeed, but it failed with:" >&2
  echo "$third_output" >&2
  exit 1
fi
