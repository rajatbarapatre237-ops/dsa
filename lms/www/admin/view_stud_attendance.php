<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());
if (isset($_GET['id'])) {
    // SQL query to fetch attendance data for the particular student including course
    $student_id = $_GET['id']; 
    $attend = $fun->fetchAttendance($student_id);
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title> Attendance</title>
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
  ?><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pa3getitle">
      <h1> Attendance</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Enrolled Students</li>
          <li class="breadcrumb-item active"> View Attendance</li>
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

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Attendance table</h5>
              
           
              <!-- Table with stripped rows -->
              <div class="table-responsive " >
         
                <table class="table datatable ">
                  <thead>

                    <tr>
                      <th scope="col">Student Id</th>
                      
                      <th scope="col">Course</th>
                      <th scope="col">Batch</th>
                      <th scope="col">Total</th>



                      <th scope="col">Present</th>
                      <th scope="col">Absent</th>
                      <th scope="col">Percentage</th>

                    </tr>
                  </thead>
                  
                  <tbody>
                  <?php 
                            if (mysqli_num_rows($attend) > 0) {
                                while ($teach = mysqli_fetch_assoc($attend)) {
                                    
                                ?>
        <tr>
            <th scope="row">
                <?php echo "DSA". $teach['sid']; ?>
            </th>
            <td>
                <?php echo $teach['course']; ?>
            </td>
            <td>
                <?php echo $teach['batch']; ?>
            </td>
            <td>
                <?php echo $teach['total_days']; ?>
            </td>
            <td>
                <?php echo  $teach['present_days']; ?>
            </td>
            <td>
                <?php echo  $teach['absent_days']; ?>
            </td>
            <td>
                <?php echo  $teach['attendance_percentage']. "%"; ?>
            </td>
        </tr>
        <?php 
                                }
                                }
                             
                            ?>
</tbody>

                </table>
              
             
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