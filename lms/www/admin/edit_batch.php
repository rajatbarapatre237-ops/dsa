<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  if(isset($_POST['submit'])){
    
      $fetch = $fun->updateBatch($_POST);
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
      header("Loaction: view_batches.php");
  }
  else{
    
    $fetch = 0;
  }
  if(isset($_GET['id'])){
    $fetch = $fun->fetchBatchById($_GET['id']);
    $fetch = mysqli_fetch_assoc($fetch);
  }
  else{
    $fetch = null;
    header("Location: view_batches.php");
  }
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Edit Batch</title>
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
        <h1>Edit Batch</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Batches</li>
            <li class="breadcrumb-item active">Edit Batches</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title"> Edit Batch Form</h5>

              <!-- No Labels Form -->
              <form class="row g-3"  action="edit_batch.php" method="POST">
                <div class="col-md-12">
                  <input type="text" class="form-control" name="name" value="<?php echo $fetch['name']?>" placeholder="Course Name">
                  <input type="text" class="form-control" name="id" value="<?php echo $fetch['id']?>" placeholder="id" hidden>
                </div>
                <div class="col-md-6">
                    <label for="start" class="form-label">Start Timing</label>
                  <input type="time" class="form-control" id="start" name="start" value="<?php echo $fetch['start_time']?>" placeholder="Start Timing">
                </div>
                <div class="col-md-6">
                <label for="end" class="form-label">Ending Timing</label>
                  <input type="time" class="form-control" id="end" name="end" value="<?php echo $fetch['end_time']?>" placeholder="Ending Time">
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