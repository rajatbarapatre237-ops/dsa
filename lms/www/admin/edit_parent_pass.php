<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());
    $id= $_GET['id'];
  if(isset($_POST['submit'])){
   $pass = $_POST['pass'];
   $id = $_POST['id'];
  $fetch = $fun->edit_parent_pass($id,$pass);
   if($fetch){
       header("Location: view_parent_pass.php");
           }
  
 }
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add Student</title>
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
      <?php 
        include "include/sideBar.php";
      ?>
  <!-- ======= Sidebar ======= -->
  
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
        <h1>Add Student</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Student</li>
            <li class="breadcrumb-item active">Edit Parent Password</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit Parent Password </h5>

              <!-- No Labels Form -->
              <form class="row g-3"  action="edit_parent_pass.php" method="POST" >
                <div class="col-md-12">
                  <input type="text" class="form-control" name="pass" placeholder="Enter New Password">
                  <input type="hidden" class="form-control" name="id" value="<?php echo $id ?>">
                </div>
               
                
                  
                
                
                <div class="text-center">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
                
              </form><!-- End No Labels Form -->

            </div>
          </div>
      </section>

    <script>
        document.getElementById('date').valueAsDate = new Date();
    </script>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
<?php 
  include "include/footer.php";
?>

</body>

</html>