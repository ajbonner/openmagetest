#!/usr/bin/env bash

set -euo pipefail

if [ -z "${OPENMAGE_ROOT-}" ]; then
  echo "Environment variable OPENMAGE_ROOT not found"
  exit 1;
fi

# Wait for mysql to be ready
timeout 60 bash -c 'until mysqladmin ping -h"mysql" --skip-ssl --silent; do sleep 1; done' || {
  echo "MySQL connection timed out"
  exit 1
}

cd "${OPENMAGE_ROOT}"

if [ -e app/etc/local.xml ]; then
  rm -rf app/etc/local.xml
fi

/usr/bin/env php install.php \
  --license_agreement_accepted true \
  --locale "en_US" \
  --timezone "America/New_York" \
  --default_currency "USD" \
  --db_host "${MYSQL_HOST}" \
  --db_name "${MYSQL_DATABASE}" \
  --db_user "${MYSQL_USER}" \
  --db_pass "${MYSQL_PASSWORD}" \
  --url "${OPENMAGE_BASEURL:-http://localhost:8080/}" \
  --secure_base_url "${OPENMAGE_SECURE_BASEURL:-https://localhost:4343/}" \
  --use_rewrites true \
  --use_secure false \
  --use_secure_admin false \
  --admin_firstname "${ADMIN_FIRSTNAME:-OpenMage}" \
  --admin_lastname "${ADMIN_LASTNAME:-Test}" \
  --admin_email "${ADMIN_EMAIL:-openmagetest@example.com}" \
  --admin_username "${ADMIN_USERNAME:-openmagetest}" \
  --admin_password "${ADMIN_PASSWORD:-Password123Password123!}" \
  --skip_url_validation

