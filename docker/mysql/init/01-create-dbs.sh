#!/usr/bin/env bash
set -e

echo "==> Creating default databases..."

mysql --user=root --password="${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS helpdesk;
EOSQL
