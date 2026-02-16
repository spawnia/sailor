#!/usr/bin/env bash
set -euo pipefail

usage() {
  echo "Usage: examples/validate.sh <example> --dependencies=highest|lowest" >&2
  exit 1
}

example=''
dependencies=''

for arg in "$@"; do
  case "$arg" in
    --dependencies=*)
      dependencies="${arg#*=}"
      ;;
    --*)
      echo "Unknown option: $arg" >&2
      usage
      ;;
    *)
      if [[ -n "$example" ]]; then
        echo "Unexpected argument: $arg" >&2
        usage
      fi
      example="$arg"
      ;;
  esac
done

if [[ -z "$example" || -z "$dependencies" ]]; then
  usage
fi

if [[ ! -d "examples/$example" ]]; then
  echo "Unknown example: $example" >&2
  exit 1
fi

cd "examples/$example"

update_args=()
case "$dependencies" in
  highest)
    ;;
  lowest)
    update_args=(--prefer-lowest --prefer-stable)
    ;;
  *)
    echo "Invalid dependencies mode: $dependencies" >&2
    usage
    ;;
esac

composer update "${update_args[@]}"
composer reinstall spawnia/sailor

if [[ "$example" == "install" ]]; then
  ./test.sh
else
  vendor/bin/sailor
  php test.php
fi
