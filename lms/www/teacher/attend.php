<?php
// Include the necessary files
include "connect/db.php";
include "connect/fun.php";

// Start the session and check for a logged-in user
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: ../login.php");
  exit();
}

$email = $_SESSION['email'];
$connect = new connect();   // Database connection
$fun = new fun($connect->dbconnect()); // Helper functions

// Handle the form submission when the submit button is pressed
if (isset($_POST['submit'])) {
    $user = $fun->getusers(); // Fetch users
    if (mysqli_num_rows($user) > 0) {
        while ($st = mysqli_fetch_assoc($user)) {
            $stu = $st['sid']; // Student ID
        }
    }

    // Fetch the selected batch and course
    $batch = $_POST['batch'];
    $cou = $_POST['course'];
    $stud = $fun->fetchstudentwithbatch($cou, $batch, $stu); // Fetch students based on the batch and course
}

// Process the attendance data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the attendance date from the form input
    $attendance_date = $_POST['attendance_date'];

    // Loop through each student's status and update the database
    if (isset($_POST['st_status']) && is_array($_POST['st_status'])) {
        $success = true; // Flag to track the success of the queries
        foreach ($_POST['st_status'] as $student_id => $status) {
            // Escape data to prevent SQL injection
            $attendance_date = mysqli_real_escape_string($connect->dbconnect(), $attendance_date);
            $student_id = mysqli_real_escape_string($connect->dbconnect(), $student_id);
            $status = mysqli_real_escape_string($connect->dbconnect(), $status);

            // Insert or update the attendance record in the database
            $query = "INSERT INTO attendance (sid, date, course, batch, status) 
                      VALUES ('$student_id', '$attendance_date', '$cou', '$batch', '$status')
                      ON DUPLICATE KEY UPDATE status = '$status'";

            // Execute the query
            if (mysqli_query($connect->dbconnect(), $query)) {
                // Success message for each student (optional, you could log this as well)
                // echo "Attendance recorded successfully for student ID: $student_id<br>";
            } else {
                // If any query fails, set the success flag to false
                $success = false;
                echo "Error recording attendance for student ID: $student_id<br>" . mysqli_error($connect->dbconnect()) . "<br>";
            }
        }

        // If all queries were successful, redirect to another page (e.g., attendance page)
        if ($success) {
            // Redirect to a success page or back to the attendance form with a success message
            header("Location: attendance.php?message=Attendance%20recorded%20successfully");
            exit();
        }
    } else {
        echo "No attendance data was submitted.";
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
      <h1>Add Attendance<?php echo $stu ?><?php echo  $cou?><?php echo  $batch ?></h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Student</li>
          <li class="breadcrumb-item active">Attendance</li>
        </ol>
      </nav>
    </div>
    <p class="text-center text-danger">
      <?php
      if (isset($_GET['msg'])) {
        echo $_GET['msg'];
      }
      ?>
    </p>
    <p class="text-center text-success">
      <?php
      if (isset($_POST['att'])) {
        echo $att_msg;
      }
      ?>
    </p>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive mt-5">
        <form action="attend.php" method="POST">
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
            if (mysqli_num_rows($stud) > 0) {
                $sr = 1;
                while ($res = mysqli_fetch_assoc($stud)) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $sr ?></th>
                        <td><?php echo $res['id'] ?></td>
                        <td><?php echo $res['course_name'] ?></td>
                        <td><?php echo $res['batch'] ?></td>
                        <td>
                            <!-- Attendance Date -->
                            <input type="date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" class="form-control" required>

                            <!-- Radio Buttons for Attendance Status -->
                            <label for="absent_<?php echo $res['id']; ?>">Absent</label>
                            <input type="radio" name="st_status[<?php echo $res['id']; ?>]" id="absent_<?php echo $res['id']; ?>" value="Absent">
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
    <button type="submit" class="btn btn-primary">Submit Attendance</button>
</form>

            </div>

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