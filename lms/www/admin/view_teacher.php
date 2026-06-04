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

  <title>View Teacher</title>
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
        <h1>View Teachers</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Teachers</li>
            <li class="breadcrumb-item active">View Teachers</li>
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
                            
                            <th scope="col"  >Action</th>
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
                           

                                  
                                              
                                           
                                          </div>
                                        </div>
                                      </div><!-- End Vertically centered Modal-->

                                    </div>
                                    <td>
                              <a href="view_teach_sal.php?teacher&email=<?php echo $res['email']?>" class="btn  btn-primary">View salary</a>
                            </td>
                                 
                            <td>
                            <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#table<?php echo $res['tid']?>">
                                        View
                                      </button> -->
                              <a href="edit_teacher.php?id=<?php echo $res['tid']?>&course=<?php echo $res['course']?>" class="btn  btn-success">Edit</a>
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

  <!-- ======= Footer ======= -->
  <?php 
  include "include/footer.php";
 ?>

</body>

</html>