#!/usr/bin/env bash

if [ -z "${OPENMAGE_ROOT-}" ]; then
  echo "Environment variable OPENMAGE_ROOT not set"
  exit 1;
fi

if [ ! $(which curl) ]; then
  apt update && apt -y install curl
fi

if [ ! $(which 7z) ]; then
  apt update && apt -y install p7zip-full
fi

curl -s https://raw.githubusercontent.com/Vinai/compressed-magento-sample-data/master/compressed-no-mp3-magento-sample-data-1.9.2.4.tar.7z \
  -o /tmp/compressed-no-mp3-magento-sample-data-1.9.2.4.tar.7z

7z x -so /tmp/compressed-no-mp3-magento-sample-data-1.9.2.4.tar.7z | tar xf - -C /tmp
cp -r /tmp/magento-sample-data-1.9.2.4/media/* "${OPENMAGE_ROOT}/media"
cp -r /tmp/magento-sample-data-1.9.2.4/skin/* "${OPENMAGE_ROOT}/skin"
mysql -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < /tmp/magento-sample-data-1.9.2.4/magento_sample_data_for_1.9.2.4.sql
