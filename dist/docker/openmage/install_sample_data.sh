#!/usr/bin/env bash

set -euo pipefail

if [ -z "${OPENMAGE_ROOT-}" ]; then
  echo "Environment variable OPENMAGE_ROOT not set"
  exit 1;
fi

if [ ! "$(which curl)" ]; then
  apt update && apt -y install curl
fi

rm -rf /tmp/magento-sample-data*
rm -rf /tmp/compressed-magento-sample-data-1.9.2.4.tgz

curl -sS -LNO --output-dir /tmp https://github.com/Vinai/compressed-magento-sample-data/raw/master/compressed-magento-sample-data-1.9.2.4.tgz ||
  (echo "Could not download sample data" && exit 1)

tar -zxf /tmp/compressed-magento-sample-data-1.9.2.4.tgz -C /tmp
cp -r /tmp/magento-sample-data-1.9.2.4/media/* "${OPENMAGE_ROOT}/media"
cp -r /tmp/magento-sample-data-1.9.2.4/skin/* "${OPENMAGE_ROOT}/skin"
mysql -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -h"${MYSQL_HOST}" "${MYSQL_DATABASE}" < /tmp/magento-sample-data-1.9.2.4/magento_sample_data_for_1.9.2.4.sql
