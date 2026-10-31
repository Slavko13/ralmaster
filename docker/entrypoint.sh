#!/bin/sh
set -eu

for writable_dir in \
    /var/www/html/bitrix/cache \
    /var/www/html/bitrix/managed_cache \
    /var/www/html/bitrix/stack_cache \
    /var/www/html/local/php_interface/logs
do
    if [ -d "$writable_dir" ]; then
        chown -R www-data:www-data "$writable_dir" 2>/dev/null || true
    fi
done

exec docker-php-entrypoint "$@"
