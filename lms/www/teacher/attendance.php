<?php
include "connect/db.php";
include "connect/fun.php";
error_reporting(0);
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit();
}
$email = $_SESSION['email'];
$connect = new connect();
$fun = new fun($connect->dbconnect());

$courseRows = [];
$coursesRes = $fun->fetchassigncourse($email);
while ($row = mysqli_fetch_assoc($coursesRes)) {
  $courseRows[] = $row;
}
$sessions = $fun->getAcademicSessions();
$stud = null;
$batches = null;
$selectedSession = $_POST['session'] ?? '';
$selectedCourse = $_POST['course'] ?? '';
$selectedBatch = $_POST['batch'] ?? '';

if (isset($_POST['course']) && $selectedCourse !== '') {
  $batches = $fun->fetchbatchwithcourse($selectedCourse);
}

if (isset($_POST['submit']) && $selectedSession !== '' && $selectedCourse !== '') {
  $stud = $fun->fetchStudentsFiltered($selectedCourse, $selectedSession, $selectedBatch);
}


// Process the attendance data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the attendance date from the form input
  $attendance_date = $_POST['attendance_date'];
  $batch = $_POST['batch'];
  $cou = $_POST['course'];

  // Loop through each student's status and update the database
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the attendance date from the form input
    $attendance_date = $_POST['attendance_date'];
    $batch = $_POST['batch'];
    $cou = $_POST['course'];
  
    // Loop through each student's status and update the database
    if (isset($_POST['st_status']) && is_array($_POST['st_status'])) {
        $success = true; // Flag to track the success of the queries
        foreach ($_POST['st_status'] as $student_id => $status) {
            // Escape data to prevent SQL injection
            $attendance_date = mysqli_real_escape_string($connect->dbconnect(), $attendance_date);
            $student_id = mysqli_real_escape_string($connect->dbconnect(), $student_id);
            $status = mysqli_real_escape_string($connect->dbconnect(), $status);
            $batch = mysqli_real_escape_string($connect->dbconnect(), $batch);
            $cou = mysqli_real_escape_string($connect->dbconnect(), $cou);
  
            // Check if attendance record already exists
            $check_query = "SELECT 1 FROM attendance WHERE sid = '$student_id' AND date = '$attendance_date' LIMIT 1";
            $result = mysqli_query($connect->dbconnect(), $check_query);
  
            if (mysqli_num_rows($result) > 0) {
                // If record exists, update the attendance
                $update_query = "UPDATE attendance 
                                 SET status = '$status' 
                                 WHERE sid = '$student_id' AND date = '$attendance_date'";
  
                if (!mysqli_query($connect->dbconnect(), $update_query)) {
                    $success = false;
                    echo "Error updating attendance for student ID: $student_id<br>" . mysqli_error($connect->dbconnect()) . "<br>";
                }
            } else {
                // If no record exists, insert a new record
                $insert_query = "INSERT INTO attendance (sid, date, course, batch, status) 
                                 VALUES ('$student_id', '$attendance_date', '$cou', '$batch', '$status')";
  
                if (!mysqli_query($connect->dbconnect(), $insert_query)) {
                    $success = false;
                    echo "Error recording attendance for student ID: $student_id<br>" . mysqli_error($connect->dbconnect()) . "<br>";
                }
            }
        }
  
        // If all queries were successful, redirect to another page (e.g., attendance page)
        if ($success) {
            header("Location: attendance.php?message=Attendance%20recorded%20successfully");
            exit();
        }
    } else {
        echo "No attendance data was submitted.";
    }
  }
  
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Add Attendance</title>
  <?php include "include/links.php"; ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <?php include "include/header.php"; ?>
  <?php include "include/sideBar.php"; ?>



  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Add Attendance</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Student</li>
          <li class="breadcrumb-item active">Attendance</li>
        </ol>
      </nav>
    </div>
   
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body text-dark">
              <h5 class="card-title">Students table</h5>
              <form action="attendance.php" method="POST" class="form" id="attendanceForm">
                <div class="row g-3">
                  <div class="col-sm-3">
                    <label class="form-label">Session</label>
                    <select name="session" id="session" class="form-select" required>
                      <option value="">Select Session</option>
                      <?php if ($sessions) { while ($sess = mysqli_fetch_assoc($sessions)) { ?>
                        <option value="<?= htmlspecialchars($sess['session_name']) ?>" <?= ($selectedSession === $sess['session_name']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($sess['session_name']) ?>
                        </option>
                      <?php } } ?>
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label">Course</label>
                    <select name="course" id="course" class="form-select" onchange="this.form.submit()" required>
                      <option value="">Select Course</option>
                      <?php foreach ($courseRows as $row): ?>
                        <option value="<?= htmlspecialchars($row['course']) ?>" <?= ($selectedCourse === $row['course']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($row['course']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <label class="form-label">Batch <small class="text-muted">(optional)</small></label>
                    <select name="batch" id="batch" class="form-select">
                      <option value="">All batches</option>
                      <?php if ($batches) { while ($bat = mysqli_fetch_assoc($batches)): ?>
                        <option value="<?= htmlspecialchars($bat['name']) ?>" <?= ($selectedBatch === $bat['name']) ? 'selected' : '' ?>>
                          <?= htmlspecialchars($bat['name']) ?>
                        </option>
                      <?php } } ?>
                    </select>
                  </div>
                  <div class="col-sm-3 d-flex align-items-end">
                    <input type="submit" class="btn btn-primary w-100" name="submit" value="Load students">
                  </div>
                </div>
              </form>
            </div>
          </div>

        </div>

        <form action="attendance.php" method="POST">
        <input type="hidden" name="session" value="<?= htmlspecialchars($selectedSession) ?>">
        <input type="hidden" name="course" value="<?= htmlspecialchars($selectedCourse) ?>">
        <input type="hidden" name="batch" value="<?= htmlspecialchars($selectedBatch) ?>">
        <div style="display: flex; justify-content: center; align-items: center; height: 100px;">
    <input type="date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" class="form-control w-25" required>
</div>

    <table class="table">

        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Course</th>
                <th>Batch</th>
                <th>Attendance Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($stud && mysqli_num_rows($stud) > 0) {
                $sr = 1;
                while ($res = mysqli_fetch_assoc($stud)) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $sr ?></th>
                        <td><?php echo $res['id'] ?></td>
                        <td><?php echo $res['course_name'] ?>
                      <input type="hidden" value="<?php echo $res['course_name'] ?>" name="course"></td>
                        <td><?php echo $res['batch'] ?>
                        <input type="hidden" value="<?php echo $res['batch'] ?>" name="batch"></td></td>
                        <td>
                            <!-- Attendance Date -->
                            

                            <!-- Radio Buttons for Attendance Status -->
                            <label for="absent_<?php echo $res['id']; ?>">Absent</label>
                            <input type="radio" name="st_status[<?php echo $res['id']; ?>]" id="absent_<?php echo $res['id']; ?>" class="mx-2" value="Absent">
                            <label for="present_<?php echo $res['id']; ?>">Present</label>
                            <input type="radio" name="st_status[<?php echo $res['id']; ?>]" id="present_<?php echo $res['id']; ?>" value="Present">
                        </td>
                    </tr>
                    <?php
                    $sr++;
                }
            }
            ?>
        </tbody>
    </table>
   <div class="text-center"> <button type="submit" class="btn btn-primary">Submit Attendance</button></div>
</form>




        <!-- End Table with stripped rows -->

      </div>
      </div>

      </div>
      </div>
    </section>




  </main>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- <script>
    $(document).ready(function () {
        $('#class').on('change', function () {
            const selectedClass = $(this).val();
            if (selectedClass) {
                $.ajax({
                    url: 'getbatch.php',
                    type: 'POST',
                    data: { class: selectedClass },
                    success: function (response) {
                        $('#course').html(response);
                    },
                    error: function () {
                        alert('Error fetching batches.');
                    }
                });
            } else {
                $('#course').html('<option value="">Select Batch</option>');
            }
        });
    });
</script> -->

  <?php include "include/footer.php"; ?>
</body>

</html>