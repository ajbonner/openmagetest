#!/usr/bin/env bash

set -eu
set -o pipefail

if [ -z "${OPENMAGE_ROOT-}" ]; then
  echo "Environment variable OPENMAGE_ROOT not found"
  exit 1;
fi

# Wait for mysql to be ready
while ! mysqladmin ping -h"mysql" --silent; do
  sleep 1
done

cd "$OPENMAGE_ROOT"

if [ -e app/etc/local.xml ]; then
  rm -rf app/etc/local.xml
fi

/usr/bin/env php install.php \
  --license_agreement_accepted true \
  --locale 'en_US' \
  --timezone 'UTC' \
  --default_currency 'USD' \
  --db_host 'mysql' \
  --db_name 'magetest_dev' \
  --db_user 'magetest' \
  --db_pass 'password123' \
  --url 'http://localhost:8080/' \
  --secure_base_url 'https://localhost:4343/' \
  --use_rewrites true \
  --use_secure false \
  --use_secure_admin false \
  --admin_firstname 'Mage' \
  --admin_lastname 'Test' \
  --admin_email 'magetest@example.com' \
  --admin_username 'magetest' \
  --admin_password 'Password123Password123!' \
  --skip_url_validation

