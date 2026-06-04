#!/bin/bash
# Run LMS from www/ on external drive. Requires XAMPP MySQL running.
export COPYFILE_DISABLE=1
cd "$(dirname "$0")"
PHP="/Applications/XAMPP/xamppfiles/bin/php"
PORT="${PORT:-8080}"

echo "Starting LMS on http://localhost:${PORT}/"
echo "  Admin:   http://localhost:${PORT}/admin/"
echo "  Student: http://localhost:${PORT}/student/"
echo "  Teacher: http://localhost:${PORT}/teacher/"
echo "  Parent:  http://localhost:${PORT}/parent/"
echo "Press Ctrl+C to stop."
exec "$PHP" -d upload_max_filesize=40M -d post_max_size=40M -S "localhost:${PORT}" -t .
