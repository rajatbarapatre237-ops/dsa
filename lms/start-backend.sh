#!/bin/bash
export COPYFILE_DISABLE=1
ROOT="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT/backend/lms" || exit 1

echo "Starting Laravel API on http://0.0.0.0:8000"
echo "  Same Wi‑Fi (phone): http://<your-mac-ip>:8000/api/v1"
echo "  iOS simulator:       http://127.0.0.1:8000/api/v1"
echo "  Android emulator:    http://10.0.2.2:8000/api/v1"
echo "  (Requires MySQL — start XAMPP or local MySQL first)"
echo ""

php artisan serve --host=0.0.0.0 --port=8000
