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
  $batches = $fun->getAllBatches();
  
  if(isset($_POST['submit'])){
     if($_POST['select']== ''){
      header("Location: add_assignment.php?msg=Please Select Type");

     }
     if($_POST['dname']== ''){
      header("Location: add_assignment.php?msg=Please Type Document Name");

     }
     if($_POST['batch']== ''){
      header("Location: add_assignment.php?msg=Please Select batch");

     }
      $fetch = $fun->insertAssignment($_POST,$_FILES);
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
  }
  else{
    
    $fetch = 0;
  }
  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add Assignment</title>
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
        <h1>Add Assignment</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Teachers</li>
            <li class="breadcrumb-item active">Add assignments</li>
          </ol>
        </nav>
      </div>
      <?php 
        if(isset($_GET['msg'])){
          echo "<p class='text-danger'>".$_GET['msg']."</p>";
        }
      ?>
      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Assignment Form</h5>

              <!-- No Labels Form -->
              <form class="row g-3"  action="add_assignment.php" method="POST" enctype="multipart/form-data">
                <div class="col-md-6">
                  <select name="select" id="select" class="form-select">
                    <option value="">Select Type</option>
                    <option value="link">Link</option>
                    <option value="file">File</option>
                  </select>
                  
                </div>
                <div class="col-md-6" id="file" hidden>
                    <label for="assignment"> Upload Assignment</label>
                  <input type="file" class="form-control" id="assignment"  name="assignment" >
                </div>
                <div class="col-md-6" id="links" hidden>
                   
                  <input type="text" class="form-control" id="link"  name="link" placeholder="Add link" >
                </div>
                <div class="col-md-6">
                    <select name="batch" id="batch" class="form-select">
                        <option value="">Select Batch</option>
                        <?php 
                            if(mysqli_num_rows($batches)>0){
                                while($res = mysqli_fetch_assoc($batches)){       
                        ?>
                                    <option value="<?php echo $res['name']?>"><?php echo $res['name']?></option>
                        <?php
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" name="dname" id="dname" class="form-control" placeholder="Enter Document Name">
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
<script>
  var select = document.getElementById("select");
  select.addEventListener("change",function (){
    var value = select.value;
    if(value=="link"){
      document.getElementById("links").hidden = false;
      document.getElementById("file").hidden = true;
    }
    else if(value == "file"){
      document.getElementById("file").hidden = false;
      document.getElementById("links").hidden = true;

    }
    else{
      document.getElementById("file").hidden = true;
      document.getElementById("links").hidden = true;
    }
  })
  select
</script>
  <!-- ======= Footer ======= -->
<?php 
  include "include/footer.php";
?>

</body>

</html>