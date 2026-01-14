# ===== Stage 1: donor (official MySQL 9.0, Debian/bookworm) =====
FROM mysql:9.0 AS upstream

# ===== Stage 1b: collect only the mysqld runtime deps =====
FROM upstream AS collect
RUN set -eux; \
  mkdir -p /deps; \
  ldd /usr/sbin/mysqld | awk '{print $3}' | grep -E '^/' | \
    grep -Ev '/(ld-linux|libc\.so|libm\.so|libdl\.so|libpthread\.so|librt\.so)(\.|$)' | \
    sort -u > /tmp/mysqld.deps; \
  while read -r f; do \
    d="/deps$(dirname "$f")"; mkdir -p "$d"; cp -a "$f" "$d/"; \
    if [ -L "$f" ]; then \
      tgt="$(readlink -f "$f")"; d2="/deps$(dirname "$tgt")"; mkdir -p "$d2"; cp -a "$tgt" "$d2/"; \
    fi; \
  done < /tmp/mysqld.deps

# Grab any protobuf libs defensively
RUN set -eux; \
  for p in /usr/lib /usr/lib64 /usr/local/lib /lib /usr/lib/*-linux-gnu; do \
    [ -d "$p" ] || continue; \
    find "$p" -maxdepth 1 -type f -name 'libprotobuf*.so*' -print | while read -r f; do \
      d="/deps$(dirname "$f")"; mkdir -p "$d"; cp -a "$f" "$d/"; \
    done; \
  done

# ===== Stage 2: slim runtime (Debian bookworm-slim) =====
FROM debian:bookworm-slim

LABEL org.opencontainers.image.title="MySQL 9.0 Slim (Debian)"
LABEL org.opencontainers.image.description="Slim runtime using official entrypoint and harvested deps"
LABEL maintainer="Mark Gregory <mark.gregory@pba.co.uk>"

RUN set -eux; \
  apt-get update; \
  DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
      libicu72 libssl3 zlib1g libstdc++6 libgcc-s1 \
      libaio1 libnuma1 libedit2 libncurses6 libzstd1 liblz4-1 \
      tzdata procps findutils ca-certificates \
  && rm -rf /var/lib/apt/lists/*

# mysql user + dirs
RUN groupadd -g 999 mysql \
 && useradd -r -u 999 -g mysql mysql \
 && mkdir -p /var/lib/mysql /var/run/mysqld /etc/mysql/conf.d /docker-entrypoint-initdb.d \
 && chown -R mysql:mysql /var/lib/mysql /var/run/mysqld /docker-entrypoint-initdb.d

# Bring in gosu and server/client from upstream
COPY --from=upstream /usr/local/bin/gosu /usr/local/bin/gosu
RUN chmod +x /usr/local/bin/gosu

COPY --from=upstream /usr/sbin/mysqld /usr/sbin/mysqld
COPY --from=upstream /usr/bin/mysql* /usr/bin/
COPY --from=upstream /usr/lib/mysql /usr/lib/mysql
COPY --from=upstream /usr/share/mysql* /usr/share/
COPY --from=collect  /deps/ /

# Configs + official entrypoint
COPY --from=upstream /etc/mysql /etc/mysql
COPY --from=upstream /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Dev-friendly perf defaults — fix missing newlines/typos using heredoc
RUN cat >/etc/mysql/conf.d/99-performance.cnf <<'EOF'
[mysqld]
innodb_dedicated_server=ON
innodb_redo_log_capacity=1G
innodb_flush_log_at_trx_commit=2
sync_binlog=0
table_open_cache=4000
table_open_cache_instances=16
thread_cache_size=100
innodb_io_capacity=2000
innodb_io_capacity_max=4000
log_error_verbosity=2
general_log=0
slow_query_log=0
mysqlx=0
skip_name_resolve=ON
bind-address=0.0.0.0
max_allowed_packet=1G
net_read_timeout=600
net_write_timeout=600
innodb_flush_method=O_DIRECT
innodb_flush_neighbors=0
innodb_lru_scan_depth=2048
innodb_read_io_threads=8
innodb_write_io_threads=8
tmp_table_size=256M
max_heap_table_size=256M
innodb_lock_wait_timeout=120
lc-messages-dir=/usr/share/mysql
pid-file=/var/lib/mysql/mysqld.pid
EOF

ENV PATH="/usr/sbin:/usr/bin:/usr/local/bin:${PATH}"

EXPOSE 3306 33060
VOLUME ["/var/lib/mysql"]

# ... your existing stages above ...
RUN mkdir -p /docker-entrypoint-initdb.d && chown -R mysql:mysql /docker-entrypoint-initdb.d

COPY docker/mysql/init/ /docker-entrypoint-initdb.d/
RUN chmod -R +x /docker-entrypoint-initdb.d/ || true

HEALTHCHECK --interval=10s --timeout=5s --retries=20 --start-period=30s CMD \
  mysqladmin ping -h 127.0.0.1 -uroot -p"${MYSQL_ROOT_PASSWORD:-root}" || exit 1

USER root
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["mysqld"]
