#!/usr/bin/env bash

set -eu
set -o pipefail

if [ ! $(/usr/bin/env pecl list | grep xdebug) ]; then
  pecl install xdebug
else
  echo "Xdebug already installed"
fi

cat > /usr/local/etc/php/conf.d/xdebug.ini << EOM
zend_extension=xdebug.so
xdebug.mode=off
xdebug.client_host=host.docker.internal
xdebug.discover_client_host=1
xdebug.start_with_request=0
xdebug.client_port=9000
xdebug.log_level=0
EOM

