<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());

//   $class = $fun->fetchAllCourses();
$courseee = $fun->fetchAllCourses();

// Fetch the course for the logged-in user
$course = null;
// if ($courseee) {
//     while ($row = mysqli_fetch_assoc($courseee)) {
//         $course = $row['course']; // Access the course data
//     }
// }


if(isset($_POST['submit']) ){
 
  
  $course = trim($_POST['course']);
  $fetch = $fun->get_today_attendance($course);
 

  }

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Students</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php 
    include "include/links.php";
  ?>
</head>

<body>

  <!-- ======= Header ======= -->
  <?php 
    include "include/header.php";
    
  ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php 
    include "include/sideBar.php";
   
  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
        <h1>View Attendance</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Enrolled Students</li>
            <li class="breadcrumb-item active">View Attendance</li>
          </ol>
        </nav>
      </div>
      <p class="text-center text-danger"><?php 
          if(isset($_GET['msg'])){
            echo $_GET['msg'];
          }
      ?></p>
    
               

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">View Attendance</h5>
             
              <form action="" method="post">
              <div class="d-flex ">
                <select name="course" id="course" class="form-select w-25">
                            <option selected>select Course </option>
                            <?php 
                            if (mysqli_num_rows($courseee) > 0) {
                                while ($teach = mysqli_fetch_assoc($courseee)) {
                                    
                                ?>
                                <option value="<?php echo $teach['course_name'];?>" class=""><?php echo $teach['course_name'];?></option>
                            <?php 
                                }
                                }
                             
                            ?>
                            
                        </select>
                        <button type="submit" name="submit" class="btn btn-primary mx-5">Submit</button>
                      </div>
              </form>
              <!-- Table with stripped rows -->
              <div class="table-responsive mt-5" >
              <form action="attendance.php" method="POST">
               <table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th scope="col">Student Id</th>
      <th scope="col">Name</th>
      <th scope="col">Entry Time</th>
      <th scope="col">Exit Time</th>
      <th scope="col">Present/Absent</th>
      <th scope="col">Date</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (isset($fetch) && mysqli_num_rows($fetch) > 0) {
        while ($row = mysqli_fetch_assoc($fetch)) {
            $student_id = $row['sid'];
            $student_name = $fun->getStudentNameById($student_id);
            $entry = $row['entry_time'] ?? '';
            $exit = $row['exit_time'] ?? '';
            $date = $row['date'] ?? '';

            // Determine status and row class
            if ($exit == "") {
                $status = "Pending";
                $row_class = "table-warning";
            } else {
                $status = $row['status'] ?? '';
                if (strtolower($status) == "present") {
                    $row_class = "table-success";
                } elseif (strtolower($status) == "absent") {
                    $row_class = "table-danger";
                } else {
                    $status = "Pending";
                    $row_class = "table-warning";
                }
            }

            echo "<tr class='{$row_class}'>";
            echo "<td>{$student_id}</td>";
            echo "<td>{$student_name}</td>";
            echo "<td>{$entry}</td>";
            echo "<td>{$exit}</td>";
            echo "<td>{$status}</td>";
            echo "<td>{$date}</td>";
            echo "</tr>";
        }
    } elseif (isset($_POST['submit'])) {
        echo "<tr><td colspan='6' class='text-center text-danger'>No attendance found for today.</td></tr>";
    }
    ?>
  </tbody>
</table>

                <!-- <div class="d-flex justify-content-center mt-5">
                    <input type="submit" name="att" id="att" class="btn btn-primary">

                </div> -->
                </form>
              </div>
            
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>




  </main><!-- End #main -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
   function getTodayDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const day = today.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Set the current date in all date inputs with the same name
        const dateInputs = document.getElementsByName("date");
        const todayDate = getTodayDate();
        console.log(todayDate);
        for (let i = 0; i < dateInputs.length; i++) {
            dateInputs[i].value = todayDate;
        }
  </script>
<script src="assets/js/scripts.js" type="text/javascript"></script>
  <!-- ======= Footer ======= -->
  <?php
  include "include/footer.php";
  ?>

</body>

</html>