<?php
header('Content-Type: application/json');

include "connect/db.php"; // adjust path if needed

$connect = new connect();
$conn = $connect->dbconnect();

$sid = $_POST['sid'] ?? null;
$month = $_POST['month'] ?? null;

if (!$sid || !$month) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$sql = "SELECT date, entry_time, exit_time, status 
        FROM attendance 
        WHERE `sid` = ? 
        AND DATE_FORMAT(date, '%Y-%m') = ? 
        ORDER BY date ASC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ss", $sid, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} else {
    echo json_encode(["error" => "SQL prepare failed"]);
}
?>
