# ===================================================================================
# Dockerfile.slim — dev image (php-fpm + xdebug + supervisor + queue)
# ===================================================================================

FROM php:8.1-fpm-alpine AS base
LABEL maintainer="Mark Gregory <mark.gregory@pba.co.uk>"
ENV COMPOSER_MEMORY_LIMIT=-1

# Runtime libs
RUN apk add --no-cache \
    tzdata bash unzip git openssh shadow supervisor \
    icu-libs libzip \
    libpng libjpeg-turbo freetype \
    libxml2 gmp

# Optional repo INIs (only if these files exist in your project)
COPY docker/php-fpm/opcache.ini  /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php-fpm/laravel.ini  /usr/local/etc/php/conf.d/zz-laravel.ini

# ------------------- Build stage: compile PHP extensions -------------------
FROM base AS build
RUN apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS build-base autoconf pkgconf re2c linux-headers \
      icu-dev libzip-dev zlib-dev \
      libpng-dev libjpeg-turbo-dev freetype-dev \
      libxml2-dev gmp-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" \
      gd exif pcntl zip pdo_mysql bcmath intl gmp opcache \
 && pecl install redis xdebug \
 && docker-php-ext-enable redis \
 && apk del .build-deps

# ------------------- Final stage: runtime-dev -------------------
FROM base AS runtime-dev
WORKDIR /var/www/html

# Bring compiled extensions AND enabling INIs
ENV PHP_EXT_DIR=/usr/local/lib/php/extensions/no-debug-non-zts-20210902
COPY --from=build ${PHP_EXT_DIR}/ ${PHP_EXT_DIR}/
COPY --from=build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Composer (dev convenience)
RUN php -r "copy('https://getcomposer.org/installer','/tmp/composer-setup.php');" \
 && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm -f /tmp/composer-setup.php
ENV COMPOSER_ALLOW_SUPERUSER=1

# Git safe dirs (for local packages if you use them)
RUN git config --global --add safe.directory /var/www/html || true \
 && git config --global --add safe.directory /var/www/html/packages || true

# Xdebug as a Zend extension (no stray extension= INIs)
RUN set -eux; \
    rm -f /usr/local/etc/php/conf.d/*xdebug*.ini || true; \
    EXTDIR="$(php -r "echo ini_get('extension_dir');")"; \
    { \
      echo "zend_extension=${EXTDIR}/xdebug.so"; \
      echo 'xdebug.mode=${XDEBUG_MODE:-develop,debug}'; \
      echo 'xdebug.discover_client_host=0'; \
      echo 'xdebug.client_host=host.docker.internal'; \
      echo 'xdebug.client_port=9003'; \
    } > /usr/local/etc/php/conf.d/zz-xdebug.ini

# ------------------- Supervisor: php-fpm + queue worker -------------------
# Full config with control socket so `supervisorctl` works
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor \
 && printf "%s\n" \
"[unix_http_server]" \
"file=/var/run/supervisor.sock" \
"chmod=0700" \
"" \
"[supervisord]" \
"nodaemon=true" \
"logfile=/var/log/supervisor/supervisord.log" \
"pidfile=/var/run/supervisord.pid" \
"childlogdir=/var/log/supervisor" \
"" \
"[include]" \
"files=/etc/supervisor/conf.d/*.conf" \
"" \
"[supervisorctl]" \
"serverurl=unix:///var/run/supervisor.sock" \
 > /etc/supervisord.conf \
 && printf "%s\n" \
"[program:php-fpm]" \
"command=php-fpm --nodaemonize" \
"autostart=true" \
"autorestart=true" \
"stopsignal=QUIT" \
"redirect_stderr=true" \
"stdout_logfile=/var/log/supervisor/php-fpm.log" \
 > /etc/supervisor/conf.d/php-fpm.conf \
 && printf "%s\n" \
"[program:laravel-queue]" \
"directory=/var/www/html" \
"command=php artisan queue:work --verbose --tries=3 --timeout=90" \
"autostart=true" \
"autorestart=true" \
"user=www-data" \
"numprocs=1" \
"redirect_stderr=true" \
"stdout_logfile=/var/log/supervisor/worker.log" \
 > /etc/supervisor/conf.d/laravel-worker.conf

# ------------------- Init: fix perms + seed vendor volume -------------------
# On first run, a named vendor volume is empty and hides baked vendor; install if needed.
RUN printf '%s\n' \
'#!/bin/sh' \
'set -e' \
'cd /var/www/html || exit 1' \
'mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views' \
'chown -R www-data:www-data storage bootstrap || true' \
'chmod -R ug+rwX storage bootstrap || true' \
'if [ ! -f vendor/autoload.php ] && [ -f composer.json ]; then' \
'  echo "📦 vendor empty → composer install (dev)…"' \
'  composer install --no-interaction --prefer-dist || true' \
'fi' \
'exec "$@"' \
> /usr/local/bin/laravel-init \
 && chmod +x /usr/local/bin/laravel-init

# ------------------- Optional: pre-warm vendor in the image -------------------
# (Hidden by named volume at runtime; harmless but speeds up local installs)
COPY composer.json composer.lock ./
# Uncomment if you have local packages
# COPY packages/ packages/
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts || true

# Optional minimal code copy (your bind mount will overlay anyway)
COPY artisan ./
COPY app/ app/
COPY bootstrap/ bootstrap/
COPY config/ config/
COPY database/ database/
COPY public/ public/
COPY resources/ resources/
COPY routes/ routes/

ENTRYPOINT ["/usr/local/bin/laravel-init"]
CMD ["supervisord","-c","/etc/supervisord.conf"]

EXPOSE 9000 9003
