# syntax=docker/dockerfile:1.7
# ===================================================================================
# Dockerfile.slim — smaller dev image (php-fpm only, Xdebug included but OFF by default)
# Multi-stage; builds extensions once, reuses in small runtime.
# ===================================================================================

FROM php:8.3-fpm-alpine AS base
LABEL maintainer="Mark Gregory <mark.gregory@mpba.co.uk>"
ENV COMPOSER_MEMORY_LIMIT=-1

# Create 'sail' user/group
ARG PUID=1000
ARG PGID=1000
RUN set -eux; \
  if awk -F: -v gid="${PGID}" '$3==gid{f=1} END{exit !(f)}' /etc/group; then \
    addgroup -S sail; \
  else \
    addgroup -g "${PGID}" sail; \
  fi; \
  if awk -F: -v uid="${PUID}" '$3==uid{f=1} END{exit !(f)}' /etc/passwd; then \
    adduser -D -s /bin/sh -G sail sail || true; \
  else \
    adduser -D -s /bin/sh -u "${PUID}" -G sail sail; \
  fi; \
  mkdir -p /home/sail && chown -R "${PUID}":sail /home/sail
ENV HOME=/home/sail

# Runtime libs only (trimmed, but keep what Laravel commonly needs)
RUN apk add --no-cache \
    tzdata unzip git \
    icu-libs libzip \
    libpng libjpeg-turbo freetype \
    libxml2 gmp

# Ensure app dir exists and owned by sail
RUN mkdir -p /var/www/html && chown -R sail:sail /var/www/html

# Optional repo INIs
COPY docker/php-fpm/opcache.ini  /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php-fpm/laravel.ini  /usr/local/etc/php/conf.d/zz-laravel.ini

# ---------- Build: compile PHP extensions (including Xdebug) ----------
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

# ---------- runtime-dev: FINAL STAGE ----------
FROM base AS runtime-dev
WORKDIR /var/www/html

# Bring compiled extensions AND enabling INIs
COPY --from=build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Composer (from official image)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Git safe dirs for local packages
RUN git config --global --add safe.directory /var/www/html || true \
 && git config --global --add safe.directory /var/www/html/packages || true

# Xdebug: installed, but OFF by default. Enable with: -e XDEBUG_MODE=develop,debug
RUN set -eux; \
  EXTDIR="$(php -r "echo ini_get('extension_dir');")"; \
  { \
    echo "zend_extension=${EXTDIR}/xdebug.so"; \
    echo 'xdebug.mode=${XDEBUG_MODE}'; \
    echo 'xdebug.start_with_request=${XDEBUG_START_WITH_REQUEST}'; \
    echo 'xdebug.idekey=${XDEBUG_IDEKEY}'; \
    echo 'xdebug.discover_client_host=${XDEBUG_DISCOVER_CLIENT_HOST}'; \
    echo 'xdebug.client_host=${XDEBUG_CLIENT_HOST}'; \
    echo 'xdebug.client_port=${XDEBUG_CLIENT_PORT}'; \
    echo 'xdebug.log_level=${XDEBUG_LOG_LEVEL}'; \
    echo 'xdebug.log=/tmp/xdebug.log'; \
  } > /usr/local/etc/php/conf.d/zz-xdebug.ini

# Make PHP-FPM pool run as 'sail'
RUN sed -i -E 's/^(user|group)\s*=.*/\1 = sail/g' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i -E 's@^;?listen\.owner\s*=.*@listen.owner = sail@g' /usr/local/etc/php-fpm.d/www.conf \
 && sed -i -E 's@^;?listen\.group\s*=.*@listen.group = sail@g' /usr/local/etc/php-fpm.d/www.conf

# Init: fix perms + seed vendor if empty (first run)
RUN cat > /usr/local/bin/laravel-init <<'EOF' \
 && chmod +x /usr/local/bin/laravel-init
#!/bin/sh
set -e
cd /var/www/html || exit 1
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views}
chown -R sail:sail storage bootstrap || true
chmod -R ug+rwX storage bootstrap || true
if [ ! -f vendor/autoload.php ] && [ -f composer.json ]; then
  echo "vendor empty -> composer install (dev)…"
  composer install --no-interaction --prefer-dist || true
fi
exec "$@"
EOF

ENTRYPOINT ["/usr/local/bin/laravel-init"]
CMD ["php-fpm","-F"]

EXPOSE 9000 9003
