<?php
session_start();
include "connect/db.php";
include "connect/fun.php";

$connect = new connect();
$conn = $connect->dbconnect();
$student_id = $_SESSION['parent_id'];
// $student_id = 214; // Replace with dynamic student ID if needed
$month = isset($_GET['month']) ? $_GET['month'] : null;

$attendanceData = [];
if ($month) {
    $sql = "SELECT date, entry_time, exit_time, status FROM attendance WHERE sid = ? AND DATE_FORMAT(date, '%Y-%m') = ? ORDER BY date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_id, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Attendance Details</title>
   <?php include "include/links.php"; ?>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include "include/header.php"; ?>
  <?php include "include/sideBar.php"; ?>

  <main id="main" class="main">
      
       <div class="text-center mb-4">
    <img src="assets/img/logo.png" alt="DSA Logo" style="height: 60px;" onerror="this.style.display='none';">
    <h2 class="mt-2 mb-0" style="color:#0d6efd; font-weight:700;">DSA Academy</h2>
    <p class="text-muted">Digital Parent Access Panel</p>
  </div>
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>
  <div class="container mt-5">
    <h3 class="mb-3">Attendance for Student ID: DSA<?php echo $student_id; ?> — <?php echo htmlspecialchars($month); ?></h3>
    

    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Entry Time</th>
          <th>Exit Time</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
if (!empty($attendanceData)) {
    foreach ($attendanceData as $row) {
        $status = strtolower(trim($row['status']));
        $bgClass = '';
        $displayStatus = '';

        if ($status === 'present') {
            $bgClass = 'table-success';
            $displayStatus = 'Present';
        } elseif ($status === 'absent') {
            $bgClass = 'table-danger';
            $displayStatus = 'Absent';
        } else {
            $bgClass = 'table-warning';
            $displayStatus = 'Pending';
        }

        echo "<tr class='{$bgClass}'>
            <td>" . htmlspecialchars($row['date']) . "</td>
            <td>" . htmlspecialchars($row['entry_time'] ?: '-') . "</td>
            <td>" . htmlspecialchars($row['exit_time'] ?: '-') . "</td>
            <td>" . htmlspecialchars($displayStatus) . "</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center text-muted'>No attendance records found for this month.</td></tr>";
}
?>

      </tbody>
    </table>
  </div>

  <?php include "include/footer.php"; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
