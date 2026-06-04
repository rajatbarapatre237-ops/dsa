<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  if(isset($_POST['submit'])){
    
      $fetch = $fun->updateInternships($_POST);
      try {
                            
        if($fetch){
            echo "<p class='m-10'>Added!!</p>";
        }
        else{
            throw new Exception("Message:");
        }
      }
      
      //catch exception
      catch(Exception $e) {
         echo "<p class='text-2xl mb-6 mt-0 ml-10 font-bold'>Course already available</p>";
      }
      header("Location: view_internships.php");
  }
  else{
    
    $fetch = 0;
  }
  if(isset($_GET['id'])){
    $fetch = $fun->getAllInternshipsWithId($_GET['id']);
    $fetch = mysqli_fetch_assoc($fetch);
  }
  else{
    $fetch = null;
    header("Location: view_internships.php");
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add Course</title>
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
        <h1>Edit Internships</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Internship</li>
            <li class="breadcrumb-item active">Edit Internships</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit Internship details Form</h5>

              <!-- No Labels Form -->
              <form class="row g-3"  action="edit_internships.php" method="POST">
                <div class="col-md-12">
                  <input type="text" class="form-control" name="name" value="<?php echo $fetch['name']?>" placeholder=" Name">
                  <input type="text" class="form-control" name="id" value="<?php echo $fetch['id']?>" placeholder=" id" hidden >
                </div>
                
                <div class="col-md-6">
                    <select name="type" class="form-select" id="type">
                        <option value="">Select Type of Internships</option>
                        <option value="Work From Home" <?php echo ($fetch['type'] == 'Work From Home')?("selected"):("")?>>Work From Home</option>
                        <option value="On-Site" <?php echo ($fetch['type'] == 'On-Site')?("selected"):("")?>>On-Site</option>
                    </select>
                </div>
                <div class="col-md-6">
                  <input type="text" class="form-control" id="duration" name="duration"  value="<?php echo $fetch['duration']?>" placeholder="Duration (in months)">
                </div>
                <div class="col-md-6">
                
                  <input type="text" class="form-control" id="perks" name="perks"  value="<?php echo $fetch['perks']?>" placeholder="Perks(comma seperated)">
                </div>
                <div class="col-md-6">
                
                  <input type="text" class="form-control" id="skills" name="skills"  value="<?php echo $fetch['skills']?>" placeholder="Required Skills (comma seperated)">
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