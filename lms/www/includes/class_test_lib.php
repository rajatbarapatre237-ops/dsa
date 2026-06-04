<?php
/**
 * Shared logic for class tests & marks (database: lms)
 */

declare(strict_types=1);

function ct_db(): mysqli
{
    static $mysqli = null;
    if ($mysqli === null) {
        require_once __DIR__ . '/../admin/connect/db.php';
        $connect = new connect();
        $mysqli = $connect->dbconnect();
        if (!$mysqli || $mysqli->connect_errno) {
            http_response_code(500);
            exit(json_encode(['ok' => false, 'error' => 'Database connection failed']));
        }
        $mysqli->set_charset('utf8mb4');
        ct_ensure_academic_schema($mysqli);
    }
    return $mysqli;
}

/** Create academic_sessions table and stud_details.session_name if missing. */
function ct_ensure_academic_schema(mysqli $db): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    $db->query(
        "CREATE TABLE IF NOT EXISTS `academic_sessions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `session_name` varchar(50) NOT NULL,
          `status` tinyint(1) NOT NULL DEFAULT 1,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `session_name` (`session_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );
    $col = $db->query("SHOW COLUMNS FROM `stud_details` LIKE 'session_name'");
    if ($col && $col->num_rows === 0) {
        $db->query("ALTER TABLE `stud_details` ADD COLUMN `session_name` varchar(50) DEFAULT NULL AFTER `batch`");
    }
}

function ct_list_sessions(): array
{
    $db = ct_db();
    $res = $db->query('SELECT id, session_name FROM academic_sessions WHERE status = 1 ORDER BY session_name DESC');
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = ['id' => (int) $row['id'], 'session_name' => $row['session_name']];
    }
    return $rows;
}

function ct_session_name(int $sessionId): ?string
{
    if ($sessionId <= 0) {
        return null;
    }
    $db = ct_db();
    $stmt = $db->prepare('SELECT session_name FROM academic_sessions WHERE id = ? AND status = 1 LIMIT 1');
    $stmt->bind_param('i', $sessionId);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    return $r['session_name'] ?? null;
}

function ct_list_batches_for_course(int $courseId): array
{
    $cn = ct_course_name($courseId);
    if ($cn === null) {
        return [];
    }
    $db = ct_db();
    $stmt = $db->prepare(
        'SELECT name, start_time, end_time FROM batches WHERE course = ? AND status = 1 ORDER BY name ASC'
    );
    $stmt->bind_param('s', $cn);
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $rows[] = [
            'name' => $row['name'],
            'label' => $row['name'] . ' (' . $row['start_time'] . ' - ' . $row['end_time'] . ')',
        ];
    }
    return $rows;
}

function ct_json(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function ct_course_name(int $courseId): ?string
{
    $db = ct_db();
    $stmt = $db->prepare('SELECT course_name FROM course_details WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    return $r['course_name'] ?? null;
}

function ct_subject_belongs_to_course(int $courseId, int $subjectId): bool
{
    $cn = ct_course_name($courseId);
    if ($cn === null) {
        return false;
    }
    $db = ct_db();
    $stmt = $db->prepare('SELECT id FROM subject WHERE id = ? AND course_name = ? LIMIT 1');
    $stmt->bind_param('is', $subjectId, $cn);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function ct_teacher_assigned(string $email, int $courseId, int $subjectId): bool
{
    $cn = ct_course_name($courseId);
    if ($cn === null) {
        return false;
    }
    $db = ct_db();
    $stmt = $db->prepare(
        'SELECT s.id FROM subject s
         INNER JOIN courses_subjects cs ON cs.course_name = s.course_name AND cs.subject_name = s.subject_name
         WHERE s.id = ? AND s.course_name = ? AND cs.teacher_email = ? LIMIT 1'
    );
    $stmt->bind_param('iss', $subjectId, $cn, $email);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function ct_list_courses_admin(?string $sessionName = null): array
{
    $db = ct_db();
    if ($sessionName !== null && $sessionName !== '') {
        $stmt = $db->prepare(
            'SELECT DISTINCT cd.id, cd.course_name FROM course_details cd
             INNER JOIN stud_details sd ON sd.course_name = cd.course_name
             WHERE sd.session_name = ?
             ORDER BY cd.course_name ASC'
        );
        $stmt->bind_param('s', $sessionName);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $db->query('SELECT id, course_name FROM course_details ORDER BY course_name ASC');
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = ['id' => (int) $row['id'], 'course_name' => $row['course_name']];
    }
    return $rows;
}

function ct_list_courses_teacher(string $email, ?string $sessionName = null): array
{
    $db = ct_db();
    if ($sessionName !== null && $sessionName !== '') {
        $stmt = $db->prepare(
            'SELECT DISTINCT cd.id, cd.course_name FROM course_details cd
             INNER JOIN courses_subjects cs ON cs.course_name = cd.course_name
             INNER JOIN stud_details sd ON sd.course_name = cd.course_name
             WHERE cs.teacher_email = ? AND sd.session_name = ?
             ORDER BY cd.course_name ASC'
        );
        $stmt->bind_param('ss', $email, $sessionName);
    } else {
        $stmt = $db->prepare(
            'SELECT DISTINCT cd.id, cd.course_name FROM course_details cd
             INNER JOIN courses_subjects cs ON cs.course_name = cd.course_name
             WHERE cs.teacher_email = ?
             ORDER BY cd.course_name ASC'
        );
        $stmt->bind_param('s', $email);
    }
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $rows[] = ['id' => (int) $row['id'], 'course_name' => $row['course_name']];
    }
    return $rows;
}

function ct_list_subjects_admin(int $courseId): array
{
    $cn = ct_course_name($courseId);
    if ($cn === null) {
        return [];
    }
    $db = ct_db();
    $stmt = $db->prepare('SELECT id, subject_name FROM subject WHERE course_name = ? ORDER BY subject_name ASC');
    $stmt->bind_param('s', $cn);
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $rows[] = ['id' => (int) $row['id'], 'subject_name' => $row['subject_name']];
    }
    return $rows;
}

function ct_list_subjects_teacher(string $email, int $courseId): array
{
    $cn = ct_course_name($courseId);
    if ($cn === null) {
        return [];
    }
    $db = ct_db();
    $stmt = $db->prepare(
        'SELECT DISTINCT s.id, s.subject_name FROM subject s
         INNER JOIN courses_subjects cs ON cs.course_name = s.course_name AND cs.subject_name = s.subject_name
         WHERE s.course_name = ? AND cs.teacher_email = ?
         ORDER BY s.subject_name ASC'
    );
    $stmt->bind_param('ss', $cn, $email);
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $rows[] = ['id' => (int) $row['id'], 'subject_name' => $row['subject_name']];
    }
    return $rows;
}

function ct_list_tests(int $courseId, int $subjectId, ?string $teacherEmail): array
{
    $db = ct_db();
    if ($teacherEmail !== null) {
        $stmt = $db->prepare(
            'SELECT ct.id, ct.test_name, ct.test_date, ct.total_marks, ct.passing_marks
             FROM class_tests ct
             INNER JOIN course_details cd ON cd.id = ct.course_id
             INNER JOIN subject s ON s.id = ct.subject_id
             INNER JOIN courses_subjects cs ON cs.course_name = cd.course_name AND cs.subject_name = s.subject_name
             WHERE ct.course_id = ? AND ct.subject_id = ? AND cs.teacher_email = ?
             ORDER BY ct.test_date DESC, ct.test_name ASC'
        );
        $stmt->bind_param('iis', $courseId, $subjectId, $teacherEmail);
    } else {
        $stmt = $db->prepare(
            'SELECT id, test_name, test_date, total_marks, passing_marks FROM class_tests
             WHERE course_id = ? AND subject_id = ?
             ORDER BY test_date DESC, test_name ASC'
        );
        $stmt->bind_param('ii', $courseId, $subjectId);
    }
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $rows[] = [
            'id' => (int) $row['id'],
            'test_name' => $row['test_name'],
            'test_date' => $row['test_date'],
            'total_marks' => (float) $row['total_marks'],
            'passing_marks' => (float) $row['passing_marks'],
        ];
    }
    return $rows;
}

function ct_create_test(
    string $role,
    string $createdBy,
    string $testName,
    int $courseId,
    int $subjectId,
    string $testDate,
    float $totalMarks,
    float $passingMarks
): array {
    if ($role !== 'admin' && $role !== 'teacher') {
        return ['ok' => false, 'error' => 'Forbidden'];
    }
    if ($testName === '' || !ct_subject_belongs_to_course($courseId, $subjectId)) {
        return ['ok' => false, 'error' => 'Invalid course/subject'];
    }
    if ($totalMarks <= 0 || $passingMarks < 0 || $passingMarks > $totalMarks) {
        return ['ok' => false, 'error' => 'Invalid marks configuration'];
    }
    $db = ct_db();
    $stmt = $db->prepare(
        'INSERT INTO class_tests (test_name, course_id, subject_id, test_date, total_marks, passing_marks, created_by_role, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param(
        'siisddss',
        $testName,
        $courseId,
        $subjectId,
        $testDate,
        $totalMarks,
        $passingMarks,
        $role,
        $createdBy
    );
    if (!$stmt->execute()) {
        if ($db->errno === 1062) {
            return ['ok' => false, 'error' => 'A test with this name and date already exists for this course and subject.'];
        }
        return ['ok' => false, 'error' => 'Could not save test'];
    }
    return ['ok' => true, 'id' => $db->insert_id];
}

function ct_students_for_course(int $courseId, ?string $sessionName = null, ?string $batchName = null): array
{
    $cn = ct_course_name($courseId);
    if ($cn === null) {
        return [];
    }
    $db = ct_db();
    $sql = 'SELECT id, name, uid FROM stud_details WHERE course_name = ?';
    $types = 's';
    $params = [$cn];

    if ($sessionName !== null && $sessionName !== '') {
        $sql .= ' AND session_name = ?';
        $types .= 's';
        $params[] = $sessionName;
    }
    if ($batchName !== null && $batchName !== '') {
        $sql .= ' AND batch = ?';
        $types .= 's';
        $params[] = $batchName;
    }
    $sql .= ' ORDER BY name ASC';

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $rows[] = [
            'student_id' => (int) $row['id'],
            'name' => $row['name'],
            'roll' => $row['uid'] ?? '',
        ];
    }
    return $rows;
}

function ct_get_test_row(int $testId): ?array
{
    $db = ct_db();
    $stmt = $db->prepare(
        'SELECT ct.*, cd.course_name, s.subject_name FROM class_tests ct
         INNER JOIN course_details cd ON cd.id = ct.course_id
         INNER JOIN subject s ON s.id = ct.subject_id
         WHERE ct.id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $testId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
}

function ct_existing_marks_map(int $testId): array
{
    $db = ct_db();
    $stmt = $db->prepare('SELECT student_id, marks_obtained FROM test_results WHERE test_id = ?');
    $stmt->bind_param('i', $testId);
    $stmt->execute();
    $map = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $map[(int) $row['student_id']] = (float) $row['marks_obtained'];
    }
    return $map;
}

function ct_save_marks(int $testId, array $marksByStudent, ?string $teacherEmail = null): array
{
    $test = ct_get_test_row($testId);
    if (!$test) {
        return ['ok' => false, 'error' => 'Test not found'];
    }
    $courseId = (int) $test['course_id'];
    $subjectId = (int) $test['subject_id'];
    if ($teacherEmail !== null && !ct_teacher_assigned($teacherEmail, $courseId, $subjectId)) {
        return ['ok' => false, 'error' => 'You are not assigned to this course/subject'];
    }
    $total = (float) $test['total_marks'];
    $db = ct_db();
    $stmtUpsert = $db->prepare(
        'INSERT INTO test_results (test_id, student_id, marks_obtained) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained)'
    );
    $studentIds = [];
    foreach ($marksByStudent as $sidKey => $val) {
        $sid = (int) $sidKey;
        if ($sid <= 0) {
            continue;
        }
        $m = is_numeric($val) ? (float) $val : null;
        if ($m === null) {
            continue;
        }
        if ($m < 0 || $m > $total) {
            return ['ok' => false, 'error' => 'Marks must be between 0 and ' . $total];
        }
        $studentIds[] = $sid;
        $tid = $testId;
        $stmtUpsert->bind_param('iid', $tid, $sid, $m);
        $stmtUpsert->execute();
    }
    return ['ok' => true, 'saved' => count($studentIds)];
}

function ct_results_rows(int $courseId, int $subjectId, int $testId, ?string $sessionName = null, ?string $batchName = null): array
{
    $db = ct_db();
    $sql = 'SELECT ct.test_name, ct.test_date, ct.total_marks, ct.passing_marks,
                sd.id AS student_id, sd.name AS student_name, sd.uid AS roll,
                tr.marks_obtained
         FROM class_tests ct
         INNER JOIN course_details cd ON cd.id = ct.course_id
         INNER JOIN stud_details sd ON sd.course_name = cd.course_name
         LEFT JOIN test_results tr ON tr.test_id = ct.id AND tr.student_id = sd.id
         WHERE ct.id = ? AND ct.course_id = ? AND ct.subject_id = ?';
    $types = 'iii';
    $params = [$testId, $courseId, $subjectId];

    if ($sessionName !== null && $sessionName !== '') {
        $sql .= ' AND sd.session_name = ?';
        $types .= 's';
        $params[] = $sessionName;
    }
    if ($batchName !== null && $batchName !== '') {
        $sql .= ' AND sd.batch = ?';
        $types .= 's';
        $params[] = $batchName;
    }
    $sql .= ' ORDER BY sd.name ASC';

    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $marks = $row['marks_obtained'];
        $marks = $marks !== null ? (float) $marks : null;
        $total = (float) $row['total_marks'];
        $pass = (float) $row['passing_marks'];
        $status = null;
        if ($marks !== null) {
            $status = $marks >= $pass ? 'Pass' : 'Fail';
        }
        $rows[] = [
            'student_id' => (int) $row['student_id'],
            'student_name' => $row['student_name'],
            'roll' => $row['roll'] ?? '',
            'marks_obtained' => $marks,
            'total_marks' => $total,
            'passing_marks' => $pass,
            'status' => $status,
        ];
    }
    return $rows;
}

function ct_parse_student_id(string $sidSession): int
{
    return (int) preg_replace('/\D/', '', $sidSession);
}

function ct_student_course_id(int $studentId): ?int
{
    $db = ct_db();
    $stmt = $db->prepare(
        'SELECT cd.id FROM course_details cd
         INNER JOIN stud_details sd ON sd.course_name = cd.course_name
         WHERE sd.id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? (int) $row['id'] : null;
}

function ct_filter_student_rows(array $rows, int $studentId): array
{
    return array_values(array_filter($rows, static function ($r) use ($studentId) {
        return (int) $r['student_id'] === $studentId;
    }));
}

function ct_student_all_marks(int $studentId): array
{
    if ($studentId <= 0) {
        return [];
    }
    $db = ct_db();
    $stmt = $db->prepare(
        'SELECT ct.test_date, ct.test_name, s.subject_name,
                ct.total_marks, ct.passing_marks, tr.marks_obtained
         FROM stud_details sd
         INNER JOIN course_details cd ON cd.course_name = sd.course_name
         INNER JOIN class_tests ct ON ct.course_id = cd.id
         INNER JOIN subject s ON s.id = ct.subject_id
         LEFT JOIN test_results tr ON tr.test_id = ct.id AND tr.student_id = sd.id
         WHERE sd.id = ?
         ORDER BY ct.test_date DESC, s.subject_name ASC, ct.test_name ASC'
    );
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $rows = [];
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $marks = $row['marks_obtained'];
        $marks = $marks !== null ? (float) $marks : null;
        $pass = (float) $row['passing_marks'];
        $status = null;
        if ($marks !== null) {
            $status = $marks >= $pass ? 'Pass' : 'Fail';
        }
        $rows[] = [
            'test_date' => $row['test_date'],
            'test_name' => $row['test_name'],
            'subject_name' => $row['subject_name'],
            'total_marks' => (float) $row['total_marks'],
            'passing_marks' => $pass,
            'marks_obtained' => $marks,
            'status' => $status,
        ];
    }
    return $rows;
}
