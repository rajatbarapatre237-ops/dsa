<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';


  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  
  
  if(isset($_POST['submit'])){
      $tname=trim($_POST['tname']);
      $phone=trim($_POST['phone']);
      $email =trim($_POST['email']);
      $id = trim($_POST['id']);
      $salary = trim($_POST['salary']);
      $course = trim($_POST['course']);
      if(empty($tname) || empty($phone) || empty($salary) || empty($email) || empty($salary) || empty($course)){
          echo json_encode(array('status'=> '0','message'=> 'Empty Fields'));
      }
      else{
        $fetch = $fun->updateTeacherById($id,$tname,$email,$phone,$salary,$course);
        if($fetch){
            header('Location: view_teacher.php');

        }

      }
  }
  if(isset($_GET['id']) ){
    $id=trim($_GET['id']);
    $course=trim($_GET['course']);
    $fetch = $fun->fetchTeacherWithId($id);
    $fetch = mysqli_fetch_assoc($fetch);
  }
  else{
    header('Location: view_teacher.php');
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
            <li class="breadcrumb-item">Teachers</li>
            <li class="breadcrumb-item active">Add Teacher</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add Teacher Form</h5>
               
              <!-- No Labels Form -->
              <form class="row g-3"  action="edit_teacher.php" method="POST">
                <div class="col-md-12">
                  <input type="text" class="form-control" name="tname" value="<?php echo $fetch['name']?>" placeholder="Teacher Name">
                  <input type="text" class="form-control" name="id" placeholder="id" value="<?php echo $fetch['tid']?>" hidden>
                </div>
                <div class="col-md-6">
                   
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone no." value="<?php echo $fetch['phone']?>">
                </div>
                <div class="col-md-6">
                   
                  <input type="text" class="form-control" id="email" name="email" value="<?php echo $fetch['email']?>" placeholder="Email">
                </div>
                <div class="col-md-6">
                
                  <input type="text" class="form-control" id="salary" name="salary" value="<?php echo $fetch['salary']?>" placeholder="Salary">
                </div>
                <div class="col-md-6">
                
                <select name="course" id="course" class="form-select">
                            <option selected><?php echo $course ?> </option>
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


  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
<?php 
  include "include/footer.php";
?>

</body>

</html>