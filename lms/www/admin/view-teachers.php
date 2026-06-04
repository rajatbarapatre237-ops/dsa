<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());

  $fetch = $fun->fetchAllTeachers();
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Student</title>
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
        <h1>View Students</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Students</li>
            <li class="breadcrumb-item active">View Student</li>
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
                <h5 class="card-title">Teachers table</h5>
  
                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table  datatable">
                        <thead>
                        
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Teacher Name</th>
                            <th scope="col">Phone no.</th>
                            <th scope="col">Email</th>
                            <th scope="col">Salary</th>
                            <th scope="col">Course</th>
                            
                            
                            <th scope="col"  >Edit</th>
                            <th scope="col"  >Delete</th>
                            
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
                            <td><?php echo $res['phone']?></td>
                            <td><?php echo $res['email']?></td>
                            <td><?php echo $res['salary']?></td>
                            <td><?php echo $res['course']?></td>
                           

                                      <!-- Vertically centered Modal -->
                                      
                                      <div class="modal fade" id="table<?php echo $res['tid']?>" tabindex="<?php echo $res['tid']?>">
                                        <div class="modal-dialog modal-dialog-centered  modal-xl modal-dialog-scrollable">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title"><?php echo $res['name']?></h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                              <div class="row">
                                                <div class="col-lg-12">
                                                  <div>
                                                   <p > <span class="fw-bold"> Phone:</span> <?= $res['phone']?></p>

                                                  </div>
                                                  <div>
                                                    <p ><span class="fw-bold"> Email: </span> <?= $res['email']?></p>

                                                  </div>
                                                  <div>
                                                    <p> <span class="fw-bold">Salary: </span><?= $res['salary']?> </p>
                                                  </div>
                                                </div>

                                              </div>
                                              <div class="row">

                                              
                                                <div class="col-lg-12">
                                                <div class="container">
                                                      <div class="row table-head fw-bold ">
                                                          <div class="col-md-3">
                                                              #
                                                          </div>
                                                          <div class="col-md-3">
                                                              Class
                                                          </div>
                                                            <div class="col-md-3">
                                                            Section
                                                          </div>
                                                            <div class="col-md-3">
                                                            Course
                                                          </div>
                                                      </div>
                                                      <hr class="w-100 text-danger">
                                                      <?php 
                                                          if(mysqli_num_rows($assigned)> 0){
                                                            $sr = 1;
                                                            while($row = mysqli_fetch_assoc($assigned)){
                                                              $course = $fun->fetchCourseWithId($row['course_id']);
                                                              $courses = mysqli_fetch_assoc($course);
                                                              $class = ($row != null)?( $row['class_id']):(0);
                                                            //   $c = $fun->fetchSectionWithId($class);
                                                              $class  =  (mysqli_num_rows($c)>0)?( mysqli_fetch_assoc($c)):(null);
                                                            $dept = ($class !=null)?( $class['dept']):(0);
                                                            // $department = $fun->fetchDepartmentWithId($dept);
                                                            $dept = mysqli_fetch_assoc($department);
                                                        ?>
                                                       <div class="row striped">
                                                        <div class="col-md-3">
                                                            <?=  $sr?>
                                                          </div>
                                                          <div class="col-md-3">
                                                          <?php echo ($class != null)?($dept['name'].": ".$class['sec']):("Not Assigned")?>
                                                          </div>
                                                          <div class="col-md-3">
                                                          <?php echo ($class != null)?($class['sem']):('Not Assigned')?>
                                                          </div>
                                                        <div class="col-md-3">
                                                        <?php echo ($courses != null)?($courses['name'].": ".$courses['type']):('Not Assigned')?>
                                                          </div>
                                                      </div>
                                                      <hr class="w-100 text-danger">
                                                        <?php 
                                                          $sr++;
                                                          }

                                                        }
                                                        ?>
                                                      
                                                      
                                                  </div>
                                                </div> 

                                              </div>
                                            </div>
                                            <div class="modal-footer">
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                              
                                            </div>
                                          </div>
                                        </div>
                                      </div><!-- End Vertically centered Modal-->

                                    </div>
                                 
                            <td>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#table<?php echo $res['tid']?>">
                                        View
                                      </button>
                              <a href="edit_teacher.php?id=<?php echo $res['tid']?>" class="btn  btn-success">Edit</a>
                            </td>
                            <td>
                              <a href="delete.php?teacher&id=<?php echo $res['tid']?>" class="btn  btn-danger">Delete</a>
                            </td>
                            
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
    <script>
        async function myfun(id, status){
            fetch(`verify.php?stud&id=${id}&verify=${status}`)
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