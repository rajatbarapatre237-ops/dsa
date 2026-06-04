<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());

  $fetch = $fun->fetchAllUsers();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Enrolled Students</title>
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
        <h1>View Enrolled Students</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Users</li>
            <li class="breadcrumb-item active">View Users</li>
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
                <h5 class="card-title">User's table</h5>
  
                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table  datatable">
                        <thead>
                        
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">StId</th>
                            <th scope="col">Password</th>
                            
                            <th scope="col"  >Edit</th>
                            <th scope="col"  >Delete</th>
                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php 
                            if(mysqli_num_rows($fetch)>0){
                                $sr = 1;
                                while($res = mysqli_fetch_assoc($fetch)){
                                    
                                    
                                   // print_r($course_taken);

                                
                        ?>
                          <tr>
                            <th scope="row"><?php echo $sr?></th>
                            <td><?php echo "DSA".$res['sid']?></td>
                            <td><?php echo $res['pass']?></td>
                            <!-- <td  class="d-inline">
                              <a href="studView.php?id=<?php //echo $res['id'] ?>" class="btn w-auto btn-info">View</a>
                            </td> -->
                            <td>
                              <a href="#" class="btn  btn-success">Edit</a>
                            </td>
                            <td>
                              <a href="#" class="btn  btn-danger">Delete</a>
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