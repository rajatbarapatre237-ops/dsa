# LMS Project

```
lms/
├── www/                 # Legacy PHP web (admin, student, teacher, parent)
├── backend/lms/         # Single Laravel API for all mobile apps
├── app/
│   ├── student/         # React Native — Student (iOS & Android)
│   ├── teacher/         # React Native — Teacher
│   └── parent/          # React Native — Parent
├── start-server.sh      # Legacy PHP (www/)
└── start-backend.sh     # Laravel API
```

## Architecture

| Layer | Description |
|-------|-------------|
| **www/** | Existing PHP panels — reference implementation |
| **backend/lms** | One REST API (`/api/v1/...`) backed by MySQL `lms` |
| **app/student, teacher, parent** | Three separate React Native apps (KidCapsule-style) |

## Laravel API

Start XAMPP **MySQL**, then:

```bash
./start-backend.sh
```

Base URL: `http://127.0.0.1:8000/api/v1`

| Role | Login |
|------|--------|
| Teacher | `POST /auth/teacher/login` `{ "email", "password" }` |
| Student | `POST /auth/student/login` `{ "student_id": "ACE123", "password" }` |
| Parent | `POST /auth/parent/login` `{ "student_id": "123", "password" }` |

Protected routes use header: `Authorization: Bearer <token>`

Examples:
- `GET /api/v1/teacher/dashboard`
- `GET /api/v1/student/dashboard`
- `GET /api/v1/parent/dashboard`

## Mobile apps — pages mapped from `www/`

| www (PHP) | Student app | Teacher app | Parent app |
|-----------|-------------|-------------|------------|
| dashboard | Home tab | Home tab | Home tab |
| profile | Account → Profile | — | — |
| invoice / courses | Academics → Courses | — | — |
| attendance | Academics → Attendance | Attendance tab | Attendance tab |
| transaction | Academics → Transactions | — | — |
| assignment | Assignments tab | Work → Assignments | — |
| class_test_results / all_test_marks | Assignments tab | Work → Tests | Marks tab |
| View_students | — | Students tab | — |
| view_attendance | — | Attendance tab | — |
| view_salary | — | Account → Salary | — |
| months (monthly attendance) | — | — | Attendance tab |
| change_pass | Account | Account | Account |

Each app has a **top header** (branded) and **bottom navigation**.

**Teacher app forms (in app):**
- **Add Attendance** — session, course, batch, date, mark each student present/absent
- **Add Assignment** — link or file upload per batch; list with active toggle and delete
- **Create Class Test** — course, subject, name, date, marks
- **Enter Test Marks** — load students for a test and save marks

**Teacher assignment API:**
- `GET /teacher/assignments` — list (with `open_url` for link/file)
- `POST /teacher/assignments` — JSON `{ type, batch_name, document_name, link }` or multipart `file`
- `PATCH /teacher/assignments/{id}/status` — `{ status: true|false }`
- `DELETE /teacher/assignments/{id}`
- `GET /teacher/form/all-batches`

Run once: `cd backend/lms && php artisan storage:link` (serves uploaded files at `/storage/assignments/...`). Mobile apps load files via `GET /{role}/assignments/{id}/file` (Bearer token)—not the legacy PHP web panels.

**Student:** `POST /student/profile` to update profile fields.

Mobile apps use **native screens only** (hub menus, detail views, in-app file viewer). No `www/` URLs are opened in the apps.

Install dependencies (once per app, on external drive):

```bash
source env.sh
cd app/student && npm install && cd ios && pod install
cd ../../teacher && npm install && cd ios && pod install
cd ../../parent && npm install && cd ios && pod install
```

Run (separate terminals):

```bash
./start-backend.sh

cd app/student && npm start
cd app/student && npm run ios    # or npm run android
```

Repeat with `app/teacher` and `app/parent` (use different Metro ports if running multiple: `npm start -- --port 8082`).

**Android emulator** API host: `10.0.2.2:8000` (configured in each app's `src/config.ts`).

## Legacy PHP

```bash
./start-server.sh
```

→ http://localhost:8080/admin/ (and `/student/`, `/teacher/`, `/parent/`)

## AppleDouble `._` files

See `./clean-dotfiles.sh` and `source env.sh` before composer/npm/pod installs.
