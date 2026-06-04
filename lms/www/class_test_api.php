<?php
/**
 * JSON API for class tests (AJAX). Same-origin session auth.
 */
declare(strict_types=1);

session_start();

require_once __DIR__ . '/includes/class_test_lib.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function ct_is_admin(): bool
{
    return !empty($_SESSION['is_valid']) && !empty($_SESSION['username']);
}

function ct_is_teacher(): bool
{
    // Teacher login sets $_SESSION['email'] and clears is_valid (see teacher/index.php).
    // Admin login sets username + is_valid but not email — so email + !is_valid identifies teacher.
    return !empty($_SESSION['email']) && empty($_SESSION['is_valid']);
}

function ct_is_student(): bool
{
    // Student uses sid; admin uses username + is_valid (same PHP session cookie on one host).
    return !empty($_SESSION['sid']) && empty($_SESSION['is_valid']);
}

function ct_is_parent(): bool
{
    return !empty($_SESSION['parent_id']);
}

function ct_resolve_session_name(): ?string
{
    $sessionId = (int) ($_GET['session_id'] ?? $_POST['session_id'] ?? 0);
    if ($sessionId > 0) {
        return ct_session_name($sessionId);
    }
    $sn = trim((string) ($_GET['session_name'] ?? $_POST['session_name'] ?? ''));
    return $sn !== '' ? $sn : null;
}

switch ($action) {
    case 'sessions':
        if (!ct_is_admin() && !ct_is_teacher()) {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        ct_json(['ok' => true, 'sessions' => ct_list_sessions()]);

    case 'batches':
        $courseId = (int) ($_GET['course_id'] ?? 0);
        if ($courseId <= 0) {
            ct_json(['ok' => false, 'error' => 'course_id required']);
        }
        if (!ct_is_admin() && !ct_is_teacher()) {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        ct_json(['ok' => true, 'batches' => ct_list_batches_for_course($courseId)]);

    case 'courses':
        $sessionName = ct_resolve_session_name();
        if (ct_is_admin()) {
            ct_json(['ok' => true, 'courses' => ct_list_courses_admin($sessionName)]);
        }
        if (ct_is_teacher()) {
            ct_json(['ok' => true, 'courses' => ct_list_courses_teacher((string) $_SESSION['email'], $sessionName)]);
        }
        if (ct_is_student()) {
            $sidNum = ct_parse_student_id((string) $_SESSION['sid']);
            $cid = ct_student_course_id($sidNum);
            if ($cid === null) {
                ct_json(['ok' => true, 'courses' => []]);
            }
            $cn = ct_course_name($cid);
            ct_json(['ok' => true, 'courses' => [['id' => $cid, 'course_name' => $cn ?? '']], 'locked_course_id' => $cid]);
        }
        if (ct_is_parent()) {
            $sid = ct_parse_student_id((string) $_SESSION['parent_id']);
            $cid = ct_student_course_id($sid);
            if ($cid === null) {
                ct_json(['ok' => true, 'courses' => []]);
            }
            $cn = ct_course_name($cid);
            ct_json(['ok' => true, 'courses' => [['id' => $cid, 'course_name' => $cn ?? '']], 'locked_course_id' => $cid]);
        }
        ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);

    case 'subjects':
        $courseId = (int) ($_GET['course_id'] ?? $_POST['course_id'] ?? 0);
        if ($courseId <= 0) {
            ct_json(['ok' => false, 'error' => 'course_id required']);
        }
        if (ct_is_admin()) {
            ct_json(['ok' => true, 'subjects' => ct_list_subjects_admin($courseId)]);
        }
        if (ct_is_teacher()) {
            ct_json(['ok' => true, 'subjects' => ct_list_subjects_teacher((string) $_SESSION['email'], $courseId)]);
        }
        if (ct_is_student()) {
            $sidNum = ct_parse_student_id((string) $_SESSION['sid']);
            $cid = ct_student_course_id($sidNum);
            if ($cid !== $courseId) {
                ct_json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
            ct_json(['ok' => true, 'subjects' => ct_list_subjects_admin($courseId)]);
        }
        if (ct_is_parent()) {
            $sidNum = ct_parse_student_id((string) $_SESSION['parent_id']);
            $cid = ct_student_course_id($sidNum);
            if ($cid !== $courseId) {
                ct_json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
            ct_json(['ok' => true, 'subjects' => ct_list_subjects_admin($courseId)]);
        }
        ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);

    case 'tests':
        $courseId = (int) ($_GET['course_id'] ?? 0);
        $subjectId = (int) ($_GET['subject_id'] ?? 0);
        if ($courseId <= 0 || $subjectId <= 0) {
            ct_json(['ok' => false, 'error' => 'course_id and subject_id required']);
        }
        $teacherEmail = null;
        if (ct_is_teacher()) {
            $teacherEmail = (string) $_SESSION['email'];
        }
        if (!ct_is_admin() && !ct_is_teacher() && !ct_is_student() && !ct_is_parent()) {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        if (ct_is_student()) {
            $sidNum = ct_parse_student_id((string) $_SESSION['sid']);
            $cid = ct_student_course_id($sidNum);
            if ($cid !== $courseId) {
                ct_json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
        }
        if (ct_is_parent()) {
            $sidNum = ct_parse_student_id((string) $_SESSION['parent_id']);
            $cid = ct_student_course_id($sidNum);
            if ($cid !== $courseId) {
                ct_json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
        }
        ct_json(['ok' => true, 'tests' => ct_list_tests($courseId, $subjectId, $teacherEmail)]);

    case 'create_test':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ct_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        $name = trim((string) ($_POST['test_name'] ?? ''));
        $courseId = (int) ($_POST['course_id'] ?? 0);
        $subjectId = (int) ($_POST['subject_id'] ?? 0);
        $testDate = trim((string) ($_POST['test_date'] ?? ''));
        $totalMarks = (float) ($_POST['total_marks'] ?? 100);
        $passingMarks = (float) ($_POST['passing_marks'] ?? 40);

        if ($name === '' || $courseId <= 0 || $subjectId <= 0 || $testDate === '') {
            ct_json(['ok' => false, 'error' => 'Missing required fields']);
        }

        if (ct_is_admin()) {
            $by = (string) $_SESSION['username'];
            $res = ct_create_test('admin', $by, $name, $courseId, $subjectId, $testDate, $totalMarks, $passingMarks);
            ct_json($res);
        }
        if (ct_is_teacher()) {
            $email = (string) $_SESSION['email'];
            if (!ct_teacher_assigned($email, $courseId, $subjectId)) {
                ct_json(['ok' => false, 'error' => 'You cannot create a test for this course/subject']);
            }
            $res = ct_create_test('teacher', $email, $name, $courseId, $subjectId, $testDate, $totalMarks, $passingMarks);
            ct_json($res);
        }
        ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);

    case 'students_marks':
        if (!ct_is_teacher() && !ct_is_admin()) {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        $testId = (int) ($_GET['test_id'] ?? 0);
        if ($testId <= 0) {
            ct_json(['ok' => false, 'error' => 'test_id required']);
        }
        $sessionName = ct_resolve_session_name();
        $batchName = trim((string) ($_GET['batch'] ?? ''));
        $test = ct_get_test_row($testId);
        if (!$test) {
            ct_json(['ok' => false, 'error' => 'Test not found']);
        }
        if (ct_is_teacher()) {
            $email = (string) $_SESSION['email'];
            if (!ct_teacher_assigned($email, (int) $test['course_id'], (int) $test['subject_id'])) {
                ct_json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
        }
        $courseId = (int) $test['course_id'];
        $students = ct_students_for_course(
            $courseId,
            $sessionName,
            $batchName !== '' ? $batchName : null
        );
        $existing = ct_existing_marks_map($testId);
        foreach ($students as &$s) {
            $sid = $s['student_id'];
            $s['marks_obtained'] = $existing[$sid] ?? null;
        }
        unset($s);
        ct_json([
            'ok' => true,
            'test' => [
                'id' => (int) $test['id'],
                'test_name' => $test['test_name'],
                'test_date' => $test['test_date'],
                'total_marks' => (float) $test['total_marks'],
                'passing_marks' => (float) $test['passing_marks'],
                'course_name' => $test['course_name'],
                'subject_name' => $test['subject_name'],
            ],
            'students' => $students,
        ]);

    case 'save_marks':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ct_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!ct_is_teacher() && !ct_is_admin()) {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        $testId = (int) ($_POST['test_id'] ?? 0);
        $marksRaw = $_POST['marks'] ?? [];
        if ($testId <= 0 || !is_array($marksRaw)) {
            ct_json(['ok' => false, 'error' => 'Invalid payload']);
        }
        $marks = [];
        foreach ($marksRaw as $sid => $val) {
            $marks[(int) $sid] = $val;
        }
        $teacherEmail = ct_is_teacher() ? (string) $_SESSION['email'] : null;
        $res = ct_save_marks($testId, $marks, $teacherEmail);
        ct_json($res);

    case 'results':
        $courseId = (int) ($_GET['course_id'] ?? 0);
        $subjectId = (int) ($_GET['subject_id'] ?? 0);
        $testId = (int) ($_GET['test_id'] ?? 0);
        if ($courseId <= 0 || $subjectId <= 0 || $testId <= 0) {
            ct_json(['ok' => false, 'error' => 'course_id, subject_id, test_id required']);
        }
        if (!ct_is_admin() && !ct_is_teacher() && !ct_is_student() && !ct_is_parent()) {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        if (ct_is_student()) {
            $sidNum = ct_parse_student_id((string) $_SESSION['sid']);
            $cid = ct_student_course_id($sidNum);
            if ($cid !== $courseId) {
                ct_json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
        }
        if (ct_is_parent()) {
            $sidNum = ct_parse_student_id((string) $_SESSION['parent_id']);
            $cid = ct_student_course_id($sidNum);
            if ($cid !== $courseId) {
                ct_json(['ok' => false, 'error' => 'Forbidden'], 403);
            }
        }
        $test = ct_get_test_row($testId);
        if (!$test || (int) $test['course_id'] !== $courseId || (int) $test['subject_id'] !== $subjectId) {
            ct_json(['ok' => false, 'error' => 'Test not found']);
        }
        $sessionName = ct_resolve_session_name();
        $batchName = trim((string) ($_GET['batch'] ?? ''));
        $rows = ct_results_rows(
            $courseId,
            $subjectId,
            $testId,
            $sessionName,
            $batchName !== '' ? $batchName : null
        );
        $meta = [
            'test_name' => $test['test_name'],
            'test_date' => $test['test_date'],
            'total_marks' => (float) $test['total_marks'],
            'passing_marks' => (float) $test['passing_marks'],
            'course_name' => $test['course_name'],
            'subject_name' => $test['subject_name'],
        ];
        if (ct_is_student()) {
            $rows = ct_filter_student_rows($rows, ct_parse_student_id((string) $_SESSION['sid']));
        }
        if (ct_is_parent()) {
            $rows = ct_filter_student_rows($rows, ct_parse_student_id((string) $_SESSION['parent_id']));
        }
        ct_json(['ok' => true, 'meta' => $meta, 'rows' => $rows]);

    case 'students_list':
        if (!ct_is_admin()) {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        $courseId = (int) ($_GET['course_id'] ?? 0);
        if ($courseId <= 0) {
            ct_json(['ok' => false, 'error' => 'course_id required']);
        }
        $sessionName = ct_resolve_session_name();
        $batchName = trim((string) ($_GET['batch'] ?? ''));
        ct_json([
            'ok' => true,
            'students' => ct_students_for_course(
                $courseId,
                $sessionName,
                $batchName !== '' ? $batchName : null
            ),
        ]);

    case 'student_all_marks':
        // Student/parent first — admin session keys may still exist in the same PHP session.
        if (ct_is_student()) {
            $studentId = ct_parse_student_id((string) $_SESSION['sid']);
        } elseif (ct_is_parent()) {
            $studentId = ct_parse_student_id((string) $_SESSION['parent_id']);
        } elseif (ct_is_admin()) {
            $studentId = (int) ($_GET['student_id'] ?? 0);
            if ($studentId <= 0) {
                ct_json(['ok' => false, 'error' => 'student_id required']);
            }
        } else {
            ct_json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }
        if ($studentId <= 0) {
            ct_json(['ok' => false, 'error' => 'Invalid student account']);
        }
        $db = ct_db();
        $stmt = $db->prepare(
            'SELECT id, name, uid, course_name, batch, session_name FROM stud_details WHERE id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        if (!$student) {
            ct_json(['ok' => false, 'error' => 'Student not found']);
        }
        ct_json([
            'ok' => true,
            'student' => $student,
            'rows' => ct_student_all_marks($studentId),
        ]);

    default:
        ct_json(['ok' => false, 'error' => 'Unknown action'], 400);
}
