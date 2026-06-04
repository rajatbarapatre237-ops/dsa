#!/bin/bash
# Launcher — app files live in www/
export COPYFILE_DISABLE=1
exec "$(dirname "$0")/www/start-server.sh"
