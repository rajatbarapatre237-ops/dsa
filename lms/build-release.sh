#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
RELEASE_ROOT="$ROOT/release"

APPS=(teacher student parent)
NAMES=(teacher student parent)
LABELS=("DSA Teacher" "DSA Student" "DSA Parent")

mkdir -p "$RELEASE_ROOT/teacher" "$RELEASE_ROOT/student" "$RELEASE_ROOT/parent"

for i in "${!APPS[@]}"; do
  app="${APPS[$i]}"
  name="${NAMES[$i]}"
  label="${LABELS[$i]}"
  app_dir="$ROOT/app/$app"
  out_dir="$RELEASE_ROOT/$name"

  echo "==> Building release APK: $label ($app)"
  cd "$app_dir/android"
  ./gradlew assembleRelease --no-daemon -PreactNativeArchitectures=armeabi-v7a,arm64-v8a

  apk="$app_dir/android/app/build/outputs/apk/release/app-release.apk"
  if [[ ! -f "$apk" ]]; then
    echo "ERROR: Release APK not found at $apk" >&2
    exit 1
  fi

  dest="$out_dir/dsa-lms-${name}-v1.0.apk"
  cp "$apk" "$dest"
  echo "    -> $dest"

  rm -rf "$app_dir/android/app/build"
done

echo ""
echo "Done. Release APKs:"
for name in "${NAMES[@]}"; do
  ls -lh "$RELEASE_ROOT/$name/"*.apk
done
