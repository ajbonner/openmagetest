#!/usr/bin/env bash

set -euo pipefail

if [ -z "${OPENMAGE_ROOT-}" ]; then
  echo "Environment variable OPENMAGE_ROOT not found"
  exit 1
fi

/usr/bin/env bash /build/setup/openmage/install_openmage.sh
/usr/bin/env bash /build/setup/openmage/setup_openmage.sh

cd /srv/openmagetest/openmage
modman init
ln -s /build .modman/OpenMageTest
modman repair
ln -sf /build/src/tests /srv/openmagetest/openmage/tests

/usr/bin/env php -derror_reporting='~E_DEPRECATED' vendor/bin/n98-magerun --root-dir="${OPENMAGE_ROOT}" sys:setup:run

/usr/bin/env bash /build/setup/openmage/install_sample_data.sh