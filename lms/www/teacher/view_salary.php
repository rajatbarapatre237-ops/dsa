<?php 
  include "connect/db.php";
  include "connect/fun.php";
  
  session_start();
  // Check if the session 'email' is set; if not, redirect to the login page
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit(); // Ensure no further code is executed
}
  $connect = new connect();
  $fun=new fun($connect->dbconnect());
  $email=$_SESSION['email'];
  $fetch = $fun->fetchteachersalarybyemail($email);
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Salary</title>
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
    <div class="pagetitle">
        <h1> Teachers Salary History</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Teachers</li>
            <li class="breadcrumb-item active">View Salary</li>
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
                <h5 class="card-title">Teachers Salary table</h5>
  
                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table  datatable">
                        <thead>
                        
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Teacher Name</th>
                            <th scope="col">email</th>
                            <th scope="col">course</th>
                            <th scope="col">Paid</th>
                            <th scope="col">Date</th>
                            
                            
                            
                           
                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php 
                            if(mysqli_num_rows($fetch)>0){
                                $sr = 1;
                                while($res = mysqli_fetch_assoc($fetch)){
                                    // $assigned  = $fun->assignedTeacherWithId($res['tid']);
                                   
                                   // print_r($course_taken);

                                
                        ?>
                          <tr>
                            <th scope="row"><?php echo $sr?></th>
                            <td><?php echo $res['name']?></td>
                            <td><?php echo $res['email']?></td>
                            
                            <td><?php echo $res['course']?></td>
                            <td><?php echo $res['salary']?></td>
                            <td><?php echo $res['date']?></td>
                            
                           

                                  
                                              
                                           
                                          </div>
                                        </div>
                                      </div><!-- End Vertically centered Modal-->

                                    </div>
                                 
                           
                            
                          </tr>
                         <?php 
                            $sr++;
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

  <!-- ======= Footer ======= -->
  <?php 
  include "include/footer.php";
 ?>

</body>

</html>