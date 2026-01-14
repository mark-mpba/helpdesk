#!/usr/bin/env bash
set -euo pipefail
# important add this alias to your local shell
# alias sail='./sail'

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SAIL_BIN="$PROJECT_DIR/vendor/bin/sail"

cmd="${1:-}"

# Map package manager commands to the node service
if [[ "$cmd" == "npm" || "$cmd" == "yarn" || "$cmd" == "pnpm" || "$cmd" == "node" ]]; then
  shift
  # Ensure node service is running (quiet)
  "$SAIL_BIN" up -d node >/dev/null 2>&1 || true

  # Use TTY if interactive
  if [ -t 0 ] && [ -t 1 ]; then
    exec "$SAIL_BIN" exec node "$cmd" "$@"
  else
    exec "$SAIL_BIN" exec -T node "$cmd" "$@"
  fi
fi

# Otherwise, behave like normal Sail
exec "$SAIL_BIN" "$@"
