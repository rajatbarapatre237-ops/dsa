<?php
include 'connect/db.php';

$db = new connect();
$conn = $db->dbconnect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$date = date('Y-m-d');

$sql = "SELECT id, course_name, batch FROM stud_details";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching students: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $studentId = (int)$row['id'];
    $course = mysqli_real_escape_string($conn, $row['course_name']);
    $batch = mysqli_real_escape_string($conn, $row['batch']);

    // Check if attendance for this student today exists
    $check = "SELECT 1 FROM attendance WHERE sid = $studentId AND date = '$date'";
    $checkResult = mysqli_query($conn, $check);

    if (!$checkResult) {
        die("Error checking attendance: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkResult) == 0) {
        // Insert new attendance record as Absent
        $insert = "INSERT INTO attendance (sid, date, entry_time, exit_time, course, batch, status)
                   VALUES ($studentId, '$date', NULL, NULL, '$course', '$batch', 'Absent')";
        $insertResult = mysqli_query($conn, $insert);

        if (!$insertResult) {
            die("Error inserting attendance: " . mysqli_error($conn));
        }
    }
}

echo "✅ Absent students marked for today, without affecting previous records.";
?>
