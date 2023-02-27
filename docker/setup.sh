#!/usr/bin/env bash

set -eu
set -o pipefail

if [ -z "${OPENMAGE_ROOT-}" ]; then
  echo "Environment variable OPENMAGE_ROOT not found"
  exit 1;
fi

bash /srv/magetest/docker/openmage/install_openmage.sh
bash /srv/magetest/docker/openmage/setup_openmage.sh

cp -r /srv/magetest/src/{app,lib,tests} "${OPENMAGE_ROOT}/"

/usr/bin/env php -derror_reporting='~E_DEPRECATED' /usr/bin/magerun.phar --root-dir="${OPENMAGE_ROOT}" sys:setup:run
