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
  $fetch = $fun->fetchStudent($course);
 

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
             
              <form action="view_attendance.php" method="post">
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
                <table class="table  ">
                  <thead>

                    <tr>
                      
                      <th scope="col">Student Id</th>
                      <th scope="col">Name</th>
                      <th scope="col">Mobile</th>
                      <th scope="col">School Name</th>
                      <th scope="col">Email</th>
                      <th scope="col">Course Name</th>
                      
                      
                      <th scope="col">Batch</th></th>
                      <!-- <th scope="col">Course Fees</th>
                      <th scope="col">Balance Fees</th> -->
                      <th scope="col">Date of Joining</th>
                      <th scope="col">View Attendance</th>


                      

                    </tr>
                  </thead>
                  
                  <tbody>
    <?php
    if(isset($fetch)){
      if (mysqli_num_rows($fetch) > 0) {
          while ($stud = mysqli_fetch_array($fetch)) {
              // Populate table rows inside the loop
              ?>
              <tr><td><?php echo 'DSA'. $stud['id']; ?></td>
                  <td><?php echo $stud['name']; ?></td>
                  <td><?php echo $stud['mobile']; ?></td>
                  <td><?php echo $stud['school_name']; ?></td>
                  <td><?php echo $stud['email']; ?></td>
                  <td><?php echo $stud['course_name']; ?></td>
                  <td><?php echo $stud['batch']; ?></td>
                  <!-- <td><?php echo $stud['course_fees']; ?></td>
                  <td><?php echo $stud['balance_fees']; ?></td> -->
                  <td><?php echo $stud['date_of_joining']; ?></td>
                  <td><a href="view_stud_attendance.php?id=<?php echo  $stud['id']; ?>" class="btn btn-success">Attendance</a></td>
                  
              </tr>
              <?php
          }
      } else {
          // Optionally handle the case where no rows are returned
          echo "<tr><td colspan='1'>No records found</td></tr>";
      }

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