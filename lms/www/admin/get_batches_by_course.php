<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

include 'connect/db.php';
include 'include/auth_session.php';

$connect = new connect();
$db = $connect->dbconnect();

$course = trim((string) ($_GET['course'] ?? ''));
if ($course === '') {
    echo json_encode(['ok' => false, 'error' => 'course required']);
    exit;
}

$courseEsc = mysqli_real_escape_string($db, $course);
$sql = "SELECT `name`, `start_time`, `end_time` FROM `batches` WHERE `course` = '$courseEsc' AND `status` = 1 ORDER BY `name` ASC";
$res = mysqli_query($db, $sql);
$batches = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $batches[] = [
            'name' => $row['name'],
            'label' => $row['name'] . ' (' . $row['start_time'] . ' - ' . $row['end_time'] . ')',
        ];
    }
}

echo json_encode(['ok' => true, 'batches' => $batches]);
