# Source from project scripts:  source "$(dirname "$0")/../env.sh"
# Prevents macOS from creating AppleDouble (._*) files on exFAT/USB volumes.
export COPYFILE_DISABLE=1
export COPYFILE_UNPACK=0
