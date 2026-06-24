#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
RELEASE_ROOT="$ROOT/release"
VERSION="1"

APPS=(teacher student parent)
NAMES=(teacher student parent)
LABELS=("DSA Teacher" "DSA Student" "DSA Parent")

mkdir -p "$RELEASE_ROOT/teacher" "$RELEASE_ROOT/student" "$RELEASE_ROOT/parent"

# Remove previous release APK/AAB artifacts.
find "$RELEASE_ROOT" \( -name '*.apk' -o -name '*.aab' \) -delete

for i in "${!APPS[@]}"; do
  app="${APPS[$i]}"
  name="${NAMES[$i]}"
  label="${LABELS[$i]}"
  app_dir="$ROOT/app/$app"
  out_dir="$RELEASE_ROOT/$name"

  echo "==> Building release APK + AAB: $label ($app)"
  cd "$app_dir/android"
  ./gradlew assembleRelease bundleRelease --no-daemon -PreactNativeArchitectures=armeabi-v7a,arm64-v8a

  apk="$app_dir/android/app/build/outputs/apk/release/app-release.apk"
  aab="$app_dir/android/app/build/outputs/bundle/release/app-release.aab"

  if [[ ! -f "$apk" ]]; then
    echo "ERROR: Release APK not found at $apk" >&2
    exit 1
  fi
  if [[ ! -f "$aab" ]]; then
    echo "ERROR: Release AAB not found at $aab" >&2
    exit 1
  fi

  apk_dest="$out_dir/dsa-lms-${name}-v${VERSION}.apk"
  aab_dest="$out_dir/dsa-lms-${name}-v${VERSION}.aab"

  cp "$apk" "$apk_dest"
  cp "$aab" "$aab_dest"
  echo "    APK -> $apk_dest"
  echo "    AAB -> $aab_dest"

  rm -rf "$app_dir/android/app/build"
done

echo ""
echo "Done. Release artifacts:"
for name in "${NAMES[@]}"; do
  ls -lh "$RELEASE_ROOT/$name/"dsa-lms-${name}-v${VERSION}.*
done
