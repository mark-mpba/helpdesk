#!/bin/sh
set -eu

# optional: create a place for Laravel cron on Alpine
# Alpine uses /etc/crontabs/<user> instead of /etc/cron.d
if ! grep -q "artisan schedule:run" /etc/crontabs/root 2>/dev/null; then
  echo '* * * * * su -s /bin/sh -c "/usr/local/bin/php /var/www/artisan schedule:run >/dev/null 2>&1" www-data' >> /etc/crontabs/root
fi

# start crond in background if present (cronie)
if command -v crond >/dev/null 2>&1; then
  crond -s
fi

# any app-specific bootstrap you had can stay here,
# but remove references to /root/.bashrc and "service ... start"

APP="${APP:-/var/www/html}"

mkdir -p "$APP/bootstrap/cache" \
         "$APP/storage/framework/cache" \
         "$APP/storage/framework/sessions" \
         "$APP/storage/framework/views"

# Ownership & perms
chown -R www-data:www-data "$APP/storage" "$APP/bootstrap/cache"
chmod -R ug+rwX            "$APP/storage" "$APP/bootstrap/cache"

exec "$@"
