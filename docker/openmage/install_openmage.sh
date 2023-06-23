#!/usr/bin/env bash

set -eu -o pipefail

BASEDIR=$(dirname "${BASH_SOURCE:-$0}")

if [ -z "${OPENMAGE_ROOT-}" ]; then
  echo "Environment variable OPENMAGE_ROOT not set"
  exit 1;
fi

if [ -z "${OPENMAGE_VERSION-}" ]; then
  echo "Environment variable OPENMAGE_VERSION not set"
  exit 1;
fi

RELEASE_PATH="https://github.com/OpenMage/magento-lts/archive/refs/tags/%version%.tar.gz"

echo -e "Using OpenMage Release \033[0;1;32m${OPENMAGE_VERSION}\033[0m"

[ -d "$OPENMAGE_ROOT" ] && rm -rf "$OPENMAGE_ROOT"
mkdir -p "$OPENMAGE_ROOT"
echo "${RELEASE_PATH/\%version\%/$OPENMAGE_VERSION}"
curl -Ls "${RELEASE_PATH/\%version\%/$OPENMAGE_VERSION}" -o - | tar -C "$OPENMAGE_ROOT" -zx --strip-components 1
/usr/bin/env composer -d "${OPENMAGE_ROOT}" install --ignore-platform-reqs

[ ! -d "${OPENMAGE_ROOT}/app/etc/includes/" ] && mkdir -p "${OPENMAGE_ROOT}/app/etc/includes/"
cp "${BASEDIR}/patches/fix-zf1future-autoloader.php" "${OPENMAGE_ROOT}/app/etc/includes/zf1future-autoload-fix.php"
