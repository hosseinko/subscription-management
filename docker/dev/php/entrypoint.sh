#!/usr/bin/env bash
cd /www

if [ ! -d "/www/vendor" ]
then
    composer install
    php artisan migrate --force
fi

chmod -R 777 /www/storage /www/bootstrap

# Starting The Queues
nohup php artisan queue:work --tries=3 --timeout=120> /dev/null &
nohup php artisan queue:work --queue=ios_failed_check --tries=1 --timeout=10> /dev/null &
nohup php artisan queue:work --queue=android_failed_check --tries=1 --timeout=10> /dev/null &
nohup php artisan queue:work --queue=notify_started --tries=1 --timeout=10> /dev/null &
nohup php artisan queue:work --queue=notify_renewed --tries=1 --timeout=10> /dev/null &
nohup php artisan queue:work --queue=notify_canceled --tries=1 --timeout=10> /dev/null &
nohup cron -f &
nohup php-fpm &

# Start supervisord and services
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
