#!/bin/bash
# Copy shared mobile code from student → teacher & parent (keeps each app's config.ts)
export COPYFILE_DISABLE=1
ROOT="$(cd "$(dirname "$0")" && pwd)"
for role in teacher parent; do
  rsync -a "$ROOT/student/src/components/" "$ROOT/$role/src/components/"
  rsync -a "$ROOT/student/src/api/http.ts" "$ROOT/$role/src/api/"
  rsync -a "$ROOT/student/src/api/lms.ts" "$ROOT/$role/src/api/"
  rsync -a "$ROOT/student/src/storage/" "$ROOT/$role/src/storage/"
  rsync -a "$ROOT/student/src/ui/" "$ROOT/$role/src/ui/"
  rsync -a "$ROOT/student/src/screens/LoginScreen.tsx" "$ROOT/$role/src/screens/"
  rsync -a "$ROOT/student/src/screens/ChangePasswordScreen.tsx" "$ROOT/$role/src/screens/"
  rsync -a "$ROOT/student/src/screens/TestResultsScreen.tsx" "$ROOT/$role/src/screens/"
  rsync -a "$ROOT/student/src/navigation/AppNavigator.tsx" "$ROOT/$role/src/navigation/"
  rsync -a "$ROOT/student/src/navigation/types.ts" "$ROOT/$role/src/navigation/"
  cp "$ROOT/student/App.tsx" "$ROOT/$role/App.tsx"
  cp "$ROOT/student/index.js" "$ROOT/$role/index.js"
done
echo "Synced shared src to teacher & parent"
