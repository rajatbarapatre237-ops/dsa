<?php
include "connect/db.php";
include "connect/fun.php";
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$connect = new connect();
$fun = new fun($connect->dbconnect());
$email = $_SESSION['email'];

$teacherRes = $fun->fetchTeacherWithemail($email);
$teacher = $teacherRes ? mysqli_fetch_assoc($teacherRes) : null;

if (!$teacher) {
    header("Location: dashboard.php");
    exit();
}

$teacherId = (int) $teacher['tid'];
$teacherName = $teacher['name'] ?? '';
$summary = $fun->fetchMyTeacherAttendance($teacherId);
$log = $fun->fetchMyTeacherAttendanceLog($teacherId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>My Attendance</title>
  <?php include "include/links.php"; ?>
</head>
<body>
<?php include "include/header.php"; ?>
<?php include "include/sideBar.php"; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>My Attendance</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item">Attendance</li>
        <li class="breadcrumb-item active">My Attendance</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Summary — <?php echo htmlspecialchars($teacherName); ?> (T<?php echo $teacherId; ?>)</h5>
            <p class="text-muted small mb-3">Recorded via NFC card tap: first tap = entry, second tap after 1 hour = exit (marked Present).</p>
            <div class="table-responsive mb-4">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Teacher ID</th>
                    <th>Course</th>
                    <th>Total Days</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Percentage</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                if ($summary && mysqli_num_rows($summary) > 0) {
                    while ($row = mysqli_fetch_assoc($summary)) {
                        ?>
                  <tr>
                    <td>T<?php echo (int) $row['tid']; ?></td>
                    <td><?php echo htmlspecialchars($row['course'] ?? '—'); ?></td>
                    <td><?php echo (int) $row['total_days']; ?></td>
                    <td><?php echo (int) $row['present_days']; ?></td>
                    <td><?php echo (int) $row['absent_days']; ?></td>
                    <td><?php echo round((float) $row['attendance_percentage'], 1); ?>%</td>
                  </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-muted">No attendance records yet. Tap your NFC card at entry, then again after 1 hour at exit.</td></tr>';
                }
                ?>
                </tbody>
              </table>
            </div>

            <h5 class="card-title">Daily log</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Course</th>
                    <th>Entry Time</th>
                    <th>Exit Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                if ($log && mysqli_num_rows($log) > 0) {
                    while ($row = mysqli_fetch_assoc($log)) {
                        $entry = $row['entry_time'] ?? '';
                        $exit = $row['exit_time'] ?? '';
                        if ($exit === '') {
                            $status = 'Pending exit';
                            $rowClass = 'table-warning';
                        } else {
                            $status = $row['status'] ?? '';
                            $rowClass = (strtolower($status) === 'present') ? 'table-success' : '';
                        }
                        ?>
                  <tr class="<?php echo $rowClass; ?>">
                    <td><?php echo htmlspecialchars($row['date'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['course'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($entry); ?></td>
                    <td><?php echo htmlspecialchars($exit ?: '—'); ?></td>
                    <td><?php echo htmlspecialchars($status); ?></td>
                  </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-muted">No daily records found.</td></tr>';
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include "include/footer.php"; ?>
</body>
</html>
