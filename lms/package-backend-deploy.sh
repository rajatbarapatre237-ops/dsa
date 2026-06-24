#!/bin/bash
set -euo pipefail

export COPYFILE_DISABLE=1
ROOT="$(cd "$(dirname "$0")" && pwd)"
SRC="$ROOT/backend/lms"
STAGING="$ROOT/.deploy-staging/lms-api"
OUT="$ROOT/lms-api-deploy.zip"

echo "Packaging Laravel API for production..."
rm -rf "$ROOT/.deploy-staging" "$OUT"
mkdir -p "$STAGING"

rsync -a \
  --exclude '.env' \
  --exclude '.env.backup' \
  --exclude '.env.production' \
  --exclude '.git' \
  --exclude '.DS_Store' \
  --exclude 'node_modules' \
  --exclude 'public/hot' \
  --exclude 'public/build' \
  --exclude 'storage/logs/*.log' \
  --exclude 'storage/framework/cache/data/*' \
  --exclude 'storage/framework/sessions/*' \
  --exclude 'storage/framework/views/*.php' \
  --exclude 'storage/pail' \
  --exclude 'tests' \
  --exclude '.phpunit.cache' \
  --exclude '.phpunit.result.cache' \
  "$SRC/" "$STAGING/"

cp "$SRC/.env.production.example" "$STAGING/.env"

if [[ -f "$SRC/.env" ]]; then
  for key in DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD APP_URL; do
    value="$(grep -E "^${key}=" "$SRC/.env" | head -1 | cut -d= -f2- || true)"
    if [[ -n "$value" ]]; then
      if grep -q "^${key}=" "$STAGING/.env"; then
        sed -i '' "s|^${key}=.*|${key}=${value}|" "$STAGING/.env"
      else
        echo "${key}=${value}" >> "$STAGING/.env"
      fi
    fi
  done
fi

APP_KEY="$(cd "$SRC" && php artisan key:generate --show)"
sed -i '' "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" "$STAGING/.env"

echo "Refreshing vendor autoload..."
(cd "$STAGING" && composer dump-autoload --no-dev --no-scripts --optimize 2>/dev/null || true)

cat > "$STAGING/DEPLOY.md" <<'EOF'
# DSA LMS API — server setup

1. Upload `lms-api-deploy.zip` and extract on the server.
2. Point domain document root to the **`public/`** folder (important).
3. Set **PHP 8.4** in cPanel → MultiPHP Manager for this domain.
4. Open in browser:

```
https://YOUR-DOMAIN/setup.php
```

When you see `"status":"success"`, the API is ready at:

```
https://YOUR-DOMAIN/api/v1
```

**Before step 4:** edit `.env` if needed (`APP_URL`, `DB_*`).

**PHP version:** requires **PHP 8.4.1 or newer**.

**404?** Document root must be `public/`, not the parent `lms-api/` folder.

**Mobile apps:** set `API_BASE_URL` to `https://YOUR-DOMAIN/api/v1`.
EOF

mkdir -p \
  "$STAGING/storage/logs" \
  "$STAGING/storage/framework/cache/data" \
  "$STAGING/storage/framework/sessions" \
  "$STAGING/storage/framework/views" \
  "$STAGING/storage/app/public/assignments" \
  "$STAGING/storage/app/public/notes"

cd "$ROOT/.deploy-staging"
zip -rq "$OUT" lms-api -x "*.DS_Store"
rm -rf "$ROOT/.deploy-staging"

SIZE="$(du -h "$OUT" | cut -f1)"
echo ""
echo "Done: $OUT ($SIZE)"
echo "Upload → extract → open https://YOUR-DOMAIN/setup.php"
