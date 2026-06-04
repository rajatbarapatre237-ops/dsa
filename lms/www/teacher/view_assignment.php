<?php 
  include "connect/db.php";
  include "connect/fun.php";
  session_start();
// Check if the session 'email' is set; if not, redirect to the login page
if (!isset($_SESSION['email'])) {
  header("Location: index.php");
  exit(); // Ensure no further code is executed
}
  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  $result = $fun->fetchAllAssignment();
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Assignments</title>
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
        <h1>View Assignments</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Teachers</li>
            <li class="breadcrumb-item active">View Assignments</li>
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
                <h5 class="card-title">Assignment Table</h5>
                  
                
                  <div id="courses" class="table-responsive" >
                <!-- Table with stripped rows -->
                <table class="table datatable"  >
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Batch Name</th>
                      <th scope="col">Document Name</th>
                      <th scope="col">Document Type</th>
                      <th scope="col">Files</th>
                      <th scope="col">Status</th>
                      

                      <th scope="col">Action</th>
                     
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    if(mysqli_num_rows($result)>0){
                      $i = 1;
                      while($res = mysqli_fetch_assoc($result)){
                        
                  ?>
                    <tr>
                      <th scope="row"><?php echo $i;?></th>
                      <td><?php echo $res['batch_name'];?></td>
                      <td><?php echo $res['document_name'];?></td>
                      <td><?php echo $res['type'];?></td>
                      <?php 
                        if($res['type'] == 'link'){
                          echo "<td> <a href='".$res['document']."' target='_blank'> Click here</a></td>";
                        }
                        else{
                          echo "<td> <a href='./documents/".$res['document']."' target='_blank'> Click here</a></td>";
                        }
                      
                      ?>
                   
                      <td class="d-flex justify-content-center">
                                <div class="form-check form-switch">


                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" <?php echo ($res['status'])?("checked"):("")?> onclick="myfun(<?php echo $res['id'].','.$res['status']?>)">
                                      
                              </div>

                      </td>
                      
                      <td>
                          <a href="#" class="btn btn-success">Edit</a>
                          <a href="delete.php?assignment&id=<?php echo $res['id']?>" class="btn btn-danger">Delete</a>

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
            fetch(`verify.php?assignment&id=${id}&verify=${status}`)
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