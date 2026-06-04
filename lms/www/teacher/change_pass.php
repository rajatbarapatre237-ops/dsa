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
  
  if(isset($_POST['submit']) ){
      
      
      $prevpass = trim($_POST['prevpass']);
      $newpass = trim($_POST['newpass']);
      $fetch = $fun->updatepass($prevpass,$newpass);
  }
 else{
    $email=$_SESSION['email'];
    $teach = $fun->fetchTeacherWithemail($email);
 }
    
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Change Password</title>
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
        <h1>Add Teacher</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Teachers</li>
            <li class="breadcrumb-item active">Change Password</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Change Password</h5>
                <?php 
                  if(isset($_POST['submit'])){
                ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <?php 
                      if($fetch){
                        echo "Password Changed";
                      }
                      else{
                        echo "Failed to changed password";
                      }
                      ?>
                      <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                    </div>

                <?php    
                  }
                ?>
              <!-- No Labels Form -->
              <form class="row g-3"  action="change_pass.php" method="POST">
              <?php 
              if(isset($teach)){
                
                if(mysqli_num_rows($teach)>0){
                    $sr = 1;
                    while($res = mysqli_fetch_assoc($teach)){
                      
                    
                  

                                
                        ?>
              
                <div class="col-md-12">
                  <input type="text" class="form-control" value="<?php echo $res['email']?>"  name="email" placeholder="Email">
                </div>
                <div class="col-md-6">
                   
                  <input type="text" class="form-control" value="<?php echo $res['password']?>" id="prevpass" name="prevpass" placeholder="">
                </div>
                <div class="col-md-6">
                   
                  <input type="text" class="form-control"  id="newpass" name="newpass" placeholder="New Password">
                </div>
                
                
                <div class="text-center">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                  
                </div>
                <?php 
                            $sr++;
                            }
                        }
                      }
                         ?>
              </form><!-- End No Labels Form -->

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