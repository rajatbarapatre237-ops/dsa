<?php 
  include "admin/connect/db.php";
  include "admin/connect/fun.php";
  include 'admin/include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  if(isset($_POST['submit'])){
    
      $fetch = $fun->insertStudentDetailMain($_POST,$_FILES);
      try {
                            
        if($fetch){
           // echo "<p class='m-10'>Added!!</p>";
        }
        else{
            throw new Exception("Message:");
        }
      }
      
      //catch exception
      catch(Exception $e) {
         //echo "<p class='text-2xl mb-6 mt-0 ml-10 font-bold'>Course already available</p>";
      }
  }
  else{
    
    $fetch = 0;
  }
  

?>
 <?php 
                        
    $c = $fun->getCourseDetails();
                        
     $b = $fun->getAllBatches();

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
    include "admin/include/links.php";
  ?>
</head>

<body>

  <!-- ======= Header ======= -->
  <?php 
    include "admin/include/header.php";
  ?>
  <!-- End Header -->
      <?php 
        include "admin/include/sideBar.php";
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
            <li class="breadcrumb-item active">Add Student</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add Student Form</h5>

              <!-- No Labels Form -->
              <form class="row g-3"  action="add_student.php" method="POST" enctype="multipart/form-data">
                <div class="col-md-12">
                  <input type="text" class="form-control" name="name" placeholder="Student name">
                </div>
                <div class="col-md-12">
                  <label for="pfp">Upload Profile</label>
                  <input type="file" class="form-control" accept="image/*" name="pfp" placeholder="Profile Picture">
                </div>
                <div class="col-md-6">
                  <input type="text" class="form-control" name="age" placeholder="Enter Age">
                </div>
                <div class="col-md-6">
                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile No. " required />
                </div>
                <div class="col-md-6">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email Id " required />
                </div>
                <div class="col-md-6">
                    <input type="text" name="city" id="city" class="form-control" placeholder="Enter City " required />
                </div>
                <div class="col-md-6">
                    <input type="text" name="state" id="state" class="form-control" placeholder="Enter State " required />
                </div>
                
                
                    <div class="col-md-12">
                        <input type="text" name="school" id="school" class="form-control" placeholder="College Name " required />
                    </div>
                    <div class="col-md-6">
                        
                        <select name="course" id="course" class="form-select">
                            <option selected>select Course </option>
                            <?php 
                            if (mysqli_num_rows($c) > 0) {
                                while ($res = mysqli_fetch_assoc($c)) {
                                    if($res['status']){
                                ?>
                                <option value="<?php echo $res['course_name'];?>" class=""><?php echo $res['course_name'];?></option>
                            <?php }
                                }
                                }
                            else{
                                echo "<option value='no courses'>No courses available</option>";
                            }    
                            ?>
                            
                        </select>
                    </div>
                    <div class="col-md-6">
                        
                        <select name="batch" id="batch" class=" form-select">
                            <option selected>select option</option>
                            <?php 
                            if (mysqli_num_rows($b) > 0) {
                                while ($res = mysqli_fetch_assoc($b)) {
                                    if($res['status']){
                                ?>
                                <option value="<?php echo $res['name'];?>" ><?php echo $res['name']."  ".$res['start_time']." - ".$res['end_time']." ";?></option>
                            <?php 
                                }
                                }
                                }
                            else{
                                echo "<option value='no courses'>No courses available</option>";
                            }    
                            ?>
                            
                        </select>
                    </div>
                
                
                <div class="col-md-6">
                    <input type="text"  name="fees" id="fees" class="form-control" placeholder="Fees" required />
                </div>
                <div class="col-md-6">
                    <input type="text" name="aadhar" id="aadhar" class="form-control" placeholder="Aadhar Id"  />
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date of Joining</label>
                    <input type="date" name="date" id="date" class="form-control" value="" placeholder="Date of Joining" required />
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
  include "admin/include/footer.php";
?>

</body>

</html>