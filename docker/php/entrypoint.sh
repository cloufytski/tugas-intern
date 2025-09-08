#!/bin/bash

if [ ! -d "/var/www/html/vendor" ]; then
    composer install --no-cache
fi

exec "$@"
