<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  $fetch = $fun->getCourseDetails();
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Course</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php 
    include "include/links.php";
  ?>
</head>

<body>

  <!-- ======= Header ======= -->
  <?php 
    include_once "include/header.php";
  ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php 
    include_once "include/sideBar.php";
  ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
        <h1>View Course</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Courses</li>
            <li class="breadcrumb-item active">View Course</li>
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
                <h5 class="card-title">Course Table</h5>
                  
                
                  <div id="courses" class="table-responsive" >
                <!-- Table with stripped rows -->
                <table class="table datatable"  >
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">CourseId</th>
                      <th scope="col">Course Name</th>
                      <th scope="col">Course Fees</th>
                      <th scope="col">Status</th>
                      

                      <th scope="col">Edit</th>
                      <th scope="col">Delete</th>
                     
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    if(mysqli_num_rows($fetch)>0){
                      $i = 1;
                      while($res = mysqli_fetch_assoc($fetch)){
                        
                  ?>
                    <tr>
                      <th scope="row"><?php echo $i;?></th>
                      <th scope="row"><?php echo "#".$res['id'];?></th>
                      <td><?php echo $res['course_name'];?></td>
                      <td><?php echo $res['course_fees'];?></td>
                      <td class="d-flex justify-content-center">
                                <div class="form-check form-switch">

                                      <a href="verify.php?course&id=<?php echo $res['id'].'&verify='.$res['status']?>">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" <?php echo ($res['status'])?("checked"):("")?> onclick="myfun(<?php echo $res['id'].','.$res['status']?>)">
                                      </a>
                              </div>

                      </td>
                      
                      <td>
                          <a href="edit_course.php?id=<?php echo $res['id'];?>" class="btn btn-success">Edit</a>
                      </td>
                      <td>
                          <a href="delete.php?course&&id=<?php echo $res['id']?>" class="btn btn-danger">Delete</a>

                      </td>
                     
                    </tr>
                   <?php 
                   ++$i;
                      }
                    }
                    
                    else{
                      $courses = null;
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
      
     


  </main><!-- End #main -->
    <script>
        async function myfun(id, status){
            fetch(`verify.php?course&id=${id}&verify=${status}`)
            .then(res => res.text())
            .then(data => console.log(data));
        }
        
    </script>
  <!-- ======= Footer ======= -->
  <?php 
    include "include/footer.php";
  ?>

</body>

</html>