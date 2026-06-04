#!/bin/bash
# Remove macOS AppleDouble (._*) and .DS_Store from this project (exFAT-safe).
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
export COPYFILE_DISABLE=1

echo "Cleaning AppleDouble and .DS_Store under: $ROOT"
# dot_clean merges/removes ._ next to real files
dot_clean -m "$ROOT" 2>/dev/null || true
# Remove any remaining stray ._ files and .DS_Store
find "$ROOT" \( -name '._*' -o -name '.DS_Store' \) -type f -delete 2>/dev/null || true

COUNT=$(find "$ROOT" -name '._*' 2>/dev/null | wc -l | tr -d ' ')
echo "Done. Remaining ._ files: $COUNT"
