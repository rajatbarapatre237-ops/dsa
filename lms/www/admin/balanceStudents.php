<?php
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$id = $_SESSION['uname'];
$batches = $fun->getAllBatches();
$selected = "";




//print_r($tc);

if(isset($_POST['submit'])) {
 
  $batch = $_POST['batch'];
  if( empty($batch)) {
    header('Location: balanceStudents.php?msg=Enter valid option');
  }
  else {
      $batch_name = $fun->getBatchWithId($batch);
      $selected = "selected";
  }
  

}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Balance Record</title>
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

  <!-- ======= Sidebar ======= -->
  <?php
  include "include/sideBar.php";
  ?><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Balance Record</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Payments</li>
          <li class="breadcrumb-item active">Balance Record</li>
        </ol>
      </nav>
    </div>
    <p class="text-center text-danger">
      <?php
      if (isset($_GET['msg'])) {
        echo $_GET['msg'];
      }
    ?>
    </p>
    <p class="text-center text-success">
    <?php  
      if (isset($_POST['att'])) {
        echo $att_msg;
      }
      ?>
    </p>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Students table</h5>
              <form action="balanceStudents.php" method="POST" class="form">
                <div class="row">

                  <div class="col-sm-4">
                  
                    <select name="batch" id="batch" class="form-select">
                      <option value="">Select Batch</option>
                      <?php
                      if (mysqli_num_rows($batches) > 0) {
                        while ($row = mysqli_fetch_array($batches)) {
    
                         
    
                          //echo "<option value='".$row['class_id']."' $checked>".$dept['name'].": ".$row['sec']."  ".$row['sem']." </option>   ";
                      
                          ?>
                          <option value="<?php echo $row['id'] ?>" >
                            <?php echo $row['name']. " ". $row['start_time'] . " - ". $row['end_time']  ?>
                          </option>
                          <?php
                        }
                      }
                      //echo $class_option
                      
                      ?>
                    </select>
                  </div>
  
                  
                  <div class="col-sm-4">
  
                    <input type="submit" class="btn btn-primary" name="submit" id="submit" >
                  </div>
                </div>

              </form>
            <?php 
              if(isset($_POST["submit"])) {
               $stud = $fun->getBalanceStudentsWithBatch($batch_name);
                          //print_r($dept);
            ?>
              <!-- Table with stripped rows -->
              <div class="table-responsive mt-5" >
             
                <table class="table  ">
                  <thead>

                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Student Id</th>
                      <th scope="col">Name</th>
                      <th scope="col">Phone no.</th>
                      <th scope="col">Balance Fees</th>
                      <th scope="col">Course</th>
                     




                    </tr>
                  </thead>
                  
                  <tbody>
                

                    <?php
                    if (mysqli_num_rows($stud) > 0) {
                      $sr = 1;
                      while ($res = mysqli_fetch_assoc($stud)) {
                          

                        ?>
                        <tr>
                          <th scope="row">
                            <?php echo $sr ?>
                          </th>
                          <td>
                            <?php echo $res['id'] ?>
                             </td>
                          </td>
                          <td>
                            <?php echo $res['name'] ?>
                          </td>
                          <td>
                            <?php echo $res['mobile']  ?>
                          </td>
                          <td>
                            <?php echo  $res['balance_fees'] ?>
                          </td>
                          <td>
                            <?php echo $res["course_name"] ?>
                          </td>
                          
                        </tr>
                        <?php
                        $sr++;
                      }
                    }
                    ?>
                    
                  </tbody>
                </table>
                
                
              </div>
            <?php }
              ?>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>




  </main><!-- End #main -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
   function getTodayDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const day = today.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Set the current date in all date inputs with the same name
        const dateInputs = document.getElementsByName("date");
        const todayDate = getTodayDate();
        console.log(todayDate);
        for (let i = 0; i < dateInputs.length; i++) {
            dateInputs[i].value = todayDate;
        }
  </script>
<script src="assets/js/scripts.js" type="text/javascript"></script>
  <!-- ======= Footer ======= -->
  <?php
  include "include/footer.php";
  ?>

</body>

</html>