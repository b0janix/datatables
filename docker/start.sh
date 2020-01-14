
#!/usr/bin/env bash

set -e

env=${APP_ENV:-production}

if [ "$env" = "production" ]; then
    echo "Caching configuration..."
#    (cd /var/www/html && php artisan config:cache && php artisan route:cache)
fi

HOST_DOMAIN="host.docker.internal" && HOST_IP=$(ip route | awk 'NR==1 {print $3}') && echo "$HOST_IP\t$HOST_DOMAIN" >> /etc/hosts

if [ "$APP_ENV" != "production" ] ; then usermod -u 1000 www-data ; else usermod -u 1001 www-data ; fi

exec apache2-foreground
