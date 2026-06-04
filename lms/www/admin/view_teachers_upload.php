<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());

  $fetch = $fun->fetchteacherupload();
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Teacher Upload</title>
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
        <h1>View Teacher Upload</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Front Page Customisation</li>
            <li class="breadcrumb-item active">View Teacher Upload</li>
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
                <h5 class="card-title">Image table</h5>
  
                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table  datatable">
                        <thead>
                        
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Teacher Photo</th>
                            <th scope="col">Teacher Name</th>
                           
                            <th scope="col">Teacher Subject</th>
                            <th scope="col">Action</th>
                            
                            
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
                            <!-- <td><?php echo $res['img_id']?></td> -->
                            <td><img src="<?php echo $res['img_path']?>" style="height: 100px;"> </td>
                            <td><?php echo $res['img_naam']?></td>
                            
                            
                            <td><?php echo $res['img_sub']?></td>
                           

                                  
                                              
                                           
                                          </div>
                                        </div>
                                      </div><!-- End Vertically centered Modal-->

                                    </div>
                                    
                             
                            <td>
                              <a href="delete.php?teacherupload&teacher_id=<?php echo $res['id']?>" class="btn  btn-danger">Delete</a>
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