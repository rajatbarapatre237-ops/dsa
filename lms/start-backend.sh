#!/bin/bash
export COPYFILE_DISABLE=1
cd "$(dirname "$0")/backend/lms" || exit 1
php artisan serve --host=127.0.0.1 --port=8000
