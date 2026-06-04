
<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  if(isset($_POST['submit']) ){
    $tname=trim($_POST['tname']);
    $phone=trim($_POST['phone']);
    $email =trim($_POST['email']);
    $salary = trim($_POST['salary']);
    $course = trim($_POST['course']);
    if(empty($tname) || empty($phone) || empty($salary) || empty($email) || empty($salary)|| empty($course)){
        echo json_encode(array('status'=> '0','message'=> 'Empty Fields'));
    }
    else{
      if($fun->checkTeacherExists($email)){
          $fetch = $fun->addTeacher($tname,$email,$phone,$salary, $course);
         
          $fetch = $fun->addanothercourse($email,$course);

      }
      else{
        $fetch = 0;
      }

    }
}



  

?>
 <?php 
                        
    $c = $fun->getCourseDetails();
                        
    

 ?>                     
                    <!DOCTYPE html>
                    <html lang="en">
                    
                    <head>
                      <meta charset="utf-8">
                      <meta content="width=device-width, initial-scale=1.0" name="viewport">
                    
                      <title>Add Teacher</title>
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
                                <li class="breadcrumb-item">Teacher</li>
                                <li class="breadcrumb-item active">Add Teacher</li>
                              </ol>
                            </nav>
                          </div>
 
 
 <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add Teacher Form</h5>
                <?php 
                  if(isset($_POST['submit'])){
                ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                      <?php 
                      if($fetch){
                        echo "Teacher Added!";
                      }
                      else{
                        echo "Teacher already exists!";
                      }
                      ?>
                      <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                    </div>

                <?php    
                  }
                ?>
              <!-- No Labels Form -->
              <form class="row g-3"  action="add_teachers.php" method="POST">
                <div class="col-md-12">
                  <input type="text" class="form-control" name="tname" placeholder="Teacher Name">
                </div>
                <div class="col-md-6">
                   
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone no.">
                </div>
                <div class="col-md-6">
                   
                  <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                </div>
                <div class="col-md-6">
                
                  <input type="text" class="form-control" id="salary" name="salary" placeholder="Salary">
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