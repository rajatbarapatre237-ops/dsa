<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());
  $result = $fun->getAllIntern();
  if(isset($_GET['id'])){
    
      $fetch = $fun->transferIntern($_GET['id']);
      try {
                            
        if($fetch){
            echo "<p class='m-10'>Added!!</p>";
            header("Location: add_interns.php");
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

  <title>Add Interns</title>
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
        <h1>Transfer Interns</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Internship</li>
            <li class="breadcrumb-item active">Transfer Interns</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Intern Table</h5>

              <!-- No Labels Form -->
                    <div class="table-responsive">
                        <?php  
                            if (mysqli_num_rows($result) > 0) {
                                $sr = 1;
                                
                            ?>
                                
                                <table class="table datatable" >
                                    
                                    <thead>
                                        <tr>
                                            <th scope="col" class="">Sr no.</th>
                                            <th scope="col" class="">Intern Id</th>
                                            <th scope="col" class="">Name</th>
                                            <th scope="col" class="">Email</th>
                                            <th scope="col" class="">Mobile no.</th>
                                            <th scope="col" class="">Internship domain</th>
                                            
                                            <th scope="col" class="">Transfer</th>
                                            <th scope="col" class="">Delete</th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                        <?php
                                    while ($res = mysqli_fetch_assoc($result)) {
                                            ?>
                                        
                                            <tr >
                                                <th scope="row" class="" >
                                                    <p><?php echo $sr ?></p>
                                                    <input type="text" name="id" id="<?php echo $res['id'] ?>" value="<?php echo $res['id'] ?>" hidden>
                                                </th>
                                                <td  >
                                                    
                                                    <?php echo "AIN".$res['id'] ?>
                                                </td>
                                                <td >
                                                    
                                                    <?php echo $res['name'] ?>
                                                </td>
                                                <td >
                                                    
                                                    <?php echo $res['email'] ?>
                                                </td>
                                                <td >
                                                    <?php echo $res['phone_no.'] ?>
                                                </td>
                                                <td >
                                                    <?php echo $res['i_domain'] ?>
                                                </td>
                                                
                                                <td  class="">
                                                    <a href="add_interns.php?id=<?php echo $res['id'] ?>" class="btn  btn-info">Transfer</a>
                                                </td>
                                                
                                                <td>
                                                    <a href="delete.php?Reginterns&&id=<?php echo $res['id']?>" class="btn  btn-danger">Delete</a>
                                                </td>
                                                
                                                
                                                
                                                
                                            
                                            
                                            
                                            </tr>
                                            
                                        <?php
                                        $sr++;
                                    }
                                    ?>
                                    </tbody>
                                            </table> 
                                            
                                            
                                           
                                    <?php 
                            } 
                            else {
                                echo "No Intern found<br>";
                            }
                            
                           
                        ?>
                        </div>
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