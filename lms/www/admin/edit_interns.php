<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());
  $query = $fun ->getAllInternships();
  
  if(isset($_POST['sumbit'])){
    
      $fetch = $fun->updateWorkingInterns($_POST);
      try {
                            
        if($fetch){
            echo "<p class='m-10'>Added!!</p>";
            header("Location: view_interns.php");
        }
        else{
            throw new Exception("Message:");
        }
      }
      
      //catch exception
      catch(Exception $e) {
         echo "<p class='text-2xl mb-6 mt-0 ml-10 font-bold'>Course already available</p>";
      }
  }
  else{
    
    $fetch = 0;
  }
  if(isset($_GET['id'])){
        $fetch = $fun->getWorkingInternsById($_GET['id']);
        if($fetch){

            $result = mysqli_fetch_assoc($fetch);
        }
        else{
            header("Location: view_interns.php");

        }
  }
  else{
    header("Location: view_interns.php");
  }
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Edit Interns</title>
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
        <h1>Edit Interns</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Internship</li>
            <li class="breadcrumb-item active">Edit Interns</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Intern Edit Form</h5>
              <form action="edit_interns.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" name="name" id="name" placeholder="Enter Name" class="form-control" value="<?php echo $result['name']?>">
                        <input type="text" name="id" id="id" placeholder="Enter ID" class="form-control" value="<?php echo $result['id']?>" hidden>
                    </div>
                    <div class="form-group">
                        <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?php echo $result['email']?>">
                    </div>
                    <div class="form-check form-group ">
                        
                        
                        <input type="radio" id="female" name="gender" value="female" class="form-check-input" <?php echo ($result['gender']=="female")?("checked"):("")?>>
                        <label for="female" class="form-check-label mr-5"> Female </label>
                        <input type="radio" name="gender" id="male" value="male" class="form-check-input" <?php echo ($result['gender']=="male")?("checked"):("")?>>
                        <label for="male" class="form-check-label mr-5"> Male </label>
                        <input type="radio" name="gender" id="other" value="other" class="form-check-input" <?php echo ($result['gender']=="other")?("checked"):("")?>>
                        <label for="other" class="form-check-label"> Other </label>
                        </label>
                    </div>
                    <div class="form-group">
                        <input type="text" name="phone" id="number" placeholder="Enter Phone no." class="form-control" value="<?php echo $result['phone_no.']?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="city" id="city" placeholder="Enter City" class="form-control" value="<?php echo $result['city']?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="state" id="state" placeholder="Enter State" class="form-control" value="<?php echo $result['state']?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="clg" id="clg" placeholder="Enter College Name" class="form-control" value="<?php echo $result['clg_name']?>">
                    </div>
                    <div class="form-group">
                        <select name="domain" id="domain" class="form-select">
                            <option value="">Select Internships</option>
                            <?php 
                                if(mysqli_num_rows($query)){
                                    while($res = mysqli_fetch_assoc($query)){
                                ?>  
                                <option value="<?php echo $res['id']?>" <?php echo ($result['i_domain']==$res['id'])?("selected"):("")?> ><?php echo $res['name']."( ".$res['duration']." months)"?></option>
                            <?php 
                                }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group d-flex justify-content-center gap-4">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
              </form>
              <!-- No Labels Form -->
                   
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