#!/usr/bin/env bash
#
# Quick validation: lint PHP, run tests, optional code style/static analysis.
# Run from pails-auth repo root: ./validate.sh
#
set -e

REPO_ROOT="$(cd "$(dirname "$0")" && pwd)"
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

fail() { echo -e "${RED}$*${NC}"; exit 1; }
ok()   { echo -e "${GREEN}$*${NC}"; }
warn() { echo -e "${YELLOW}$*${NC}"; }

# --- 1. Lint PHP files (php -l) ---
ok "Linting PHP..."
err=0
while IFS= read -r -d '' f; do
  if ! php -l "$f" >/dev/null 2>&1; then
    php -l "$f" 2>&1 || true
    err=1
  fi
done < <(find "$REPO_ROOT" -type f -name '*.php' -not -path '*/vendor/*' -print0 2>/dev/null)
[[ $err -eq 0 ]] || fail "Lint failed."
ok "  Lint OK"

# --- 2. PHPUnit ---
ok "Running PHPUnit..."
(cd "$REPO_ROOT" && ./vendor/bin/phpunit 2>/dev/null || php vendor/bin/phpunit 2>/dev/null) || fail "PHPUnit failed."
ok "  PHPUnit OK"

# --- 3. Optional: PHP_CodeSniffer ---
if [[ -x "$REPO_ROOT/vendor/bin/phpcs" ]]; then
  warn "Running phpcs..."
  (cd "$REPO_ROOT" && ./vendor/bin/phpcs --standard=PSR12 . 2>/dev/null) || warn "  phpcs reported issues (optional)"
fi

# --- 4. Optional: PHPStan ---
if [[ -f "$REPO_ROOT/phpstan.neon" || -f "$REPO_ROOT/phpstan.neon.dist" ]]; then
  if [[ -x "$REPO_ROOT/vendor/bin/phpstan" ]]; then
    warn "Running phpstan..."
    (cd "$REPO_ROOT" && ./vendor/bin/phpstan analyse --no-progress 2>/dev/null) || warn "  phpstan reported issues (optional)"
  fi
fi

ok "Validation passed."
