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

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$attend = null;
$fetch = null;
$selectedCourse = trim($_POST['course'] ?? '');

$courseRows = [];
$coursesRes = $fun->fetchTeacherCourses($email);
if ($coursesRes) {
    while ($row = mysqli_fetch_assoc($coursesRes)) {
        $courseRows[] = $row;
    }
}

if ($studentId > 0) {
    $attend = $fun->fetchAttendance($studentId);
} elseif (isset($_POST['submit']) && $selectedCourse !== '') {
    $fetch = $fun->fetchStudent($selectedCourse);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>View Attendance</title>
  <?php include "include/links.php"; ?>
</head>
<body>
<?php include "include/header.php"; ?>
<?php include "include/sideBar.php"; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>View Attendance</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item">Attendance</li>
        <li class="breadcrumb-item active"><?php echo $studentId > 0 ? 'Student summary' : 'Students'; ?></li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <?php if ($studentId > 0) { ?>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Attendance summary — DSA<?php echo $studentId; ?></h5>
                <a href="view_attendance.php" class="btn btn-outline-secondary btn-sm">Back to list</a>
              </div>
              <div class="table-responsive">
                <table class="table datatable">
                  <thead>
                    <tr>
                      <th>Student Id</th>
                      <th>Course</th>
                      <th>Batch</th>
                      <th>Total</th>
                      <th>Present</th>
                      <th>Absent</th>
                      <th>Percentage</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  if ($attend && mysqli_num_rows($attend) > 0) {
                      while ($row = mysqli_fetch_assoc($attend)) {
                          ?>
                    <tr>
                      <td>DSA<?php echo (int) $row['sid']; ?></td>
                      <td><?php echo htmlspecialchars($row['course']); ?></td>
                      <td><?php echo htmlspecialchars($row['batch']); ?></td>
                      <td><?php echo (int) $row['total_days']; ?></td>
                      <td><?php echo (int) $row['present_days']; ?></td>
                      <td><?php echo (int) $row['absent_days']; ?></td>
                      <td><?php echo round((float) $row['attendance_percentage'], 1); ?>%</td>
                    </tr>
                          <?php
                      }
                  } else {
                      echo '<tr><td colspan="7" class="text-muted">No attendance records for this student yet.</td></tr>';
                  }
                  ?>
                  </tbody>
                </table>
              </div>
            <?php } else { ?>
              <h5 class="card-title">Students by course</h5>
              <form action="view_attendance.php" method="post" class="mb-4">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                  <select name="course" class="form-select" style="max-width:260px;" required>
                    <option value="">Select course</option>
                    <?php foreach ($courseRows as $row) { ?>
                      <option value="<?php echo htmlspecialchars($row['course']); ?>" <?php echo ($selectedCourse === $row['course']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['course']); ?>
                      </option>
                    <?php } ?>
                  </select>
                  <button type="submit" name="submit" class="btn btn-primary">Load students</button>
                </div>
              </form>
              <div class="table-responsive">
                <table class="table datatable">
                  <thead>
                    <tr>
                      <th>Student Id</th>
                      <th>Name</th>
                      <th>Course</th>
                      <th>Batch</th>
                      <th>Session</th>
                      <th>View Attendance</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  if ($fetch && mysqli_num_rows($fetch) > 0) {
                      while ($stud = mysqli_fetch_assoc($fetch)) {
                          ?>
                    <tr>
                      <td>DSA<?php echo (int) $stud['id']; ?></td>
                      <td><?php echo htmlspecialchars($stud['name']); ?></td>
                      <td><?php echo htmlspecialchars($stud['course_name']); ?></td>
                      <td><?php echo htmlspecialchars($stud['batch']); ?></td>
                      <td><?php echo htmlspecialchars($stud['session_name'] ?? '—'); ?></td>
                      <td>
                        <a href="view_attendance.php?id=<?php echo (int) $stud['id']; ?>" class="btn btn-success btn-sm">Attendance</a>
                      </td>
                    </tr>
                          <?php
                      }
                  } elseif (isset($_POST['submit'])) {
                      echo '<tr><td colspan="6" class="text-muted">No students found for this course.</td></tr>';
                  } else {
                      echo '<tr><td colspan="6" class="text-muted">Select a course and click Load students.</td></tr>';
                  }
                  ?>
                  </tbody>
                </table>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include "include/footer.php"; ?>
</body>
</html>
