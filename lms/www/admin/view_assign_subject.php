<?php 

  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());
  $courseee = $fun->fetchAllTeachers();
 

  if(isset($_POST['submit']) ){
 
  
    $email = trim($_POST['email']);
   
   
  
    }
    $fetch = $fun->fetchCourseWithemail($email);
    



  
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Teachers</title>
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
        <h1>View Assigned Subject</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Teacher</li>
            <li class="breadcrumb-item active">View Assigned Subject</li>
          </ol>
        </nav>
      </div>
      <p class="text-center text-danger"><?php 
          if(isset($_GET['msg'])){
            echo $_GET['msg'];
          }
      ?></p>
      <section class="section">
        <div class="row">
          <div class="col-lg-12">
  
            <div class="card">
            <div class="card-body">
                <h5 class="card-title">Teachers Table</h5>
                <form action="view_assign_subject.php" method="post">
              <div class="d-flex ">
                <select name="email" id="email" class="form-select w-25">
                            <option selected>select Teacher </option>
                            <?php 
                            if (mysqli_num_rows($courseee) > 0) {
                                while ($teach = mysqli_fetch_assoc($courseee)) {
                                    
                                ?>
                                <option value="<?php echo $teach['email'];?>" class=""><?php echo $teach['name'];?></option>
                            <?php 
                                }
                                }
                             
                            ?>
                            
                        </select>
                        <button type="submit" name="submit" class="btn btn-primary mx-5">Submit</button>
                        
                      </div>
              </form>
                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table  datatable">
                        <thead>
                        
                          <tr>
                            <th scope="col">#</th>
                           
                            <th scope="col">Teacher Name</th>
                            <th scope="col">Teacher Email</th>
                            <th scope="col">Course</th>
                            <th scope="col">Subject</th>
                            
                            <th scope="col"  >Edit</th>
                            <th scope="col"  >Delete</th>
                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php 
                            if(mysqli_num_rows($fetch)>0){
                                $sr = 1;
                                while($res = mysqli_fetch_assoc($fetch)){
                                    
                               
                                   // print_r($course_taken);

                                
                        ?>
                          <tr>
                            <th scope="row"><?php echo $sr?></th>
                            
                            <?php 
                            $courseee = $fun->fetchAllTeachers();
                            if(mysqli_num_rows($courseee)>0){
                                
                                while($name = mysqli_fetch_assoc($courseee)){
                                    
                               
                                   // print_r($course_taken);

                                
                        ?>
                            <td><?php echo $name['name']?></td>
                            <?php 
                           
                            }
                        }
                         ?>

                             <td><?php echo $res['teacher_email']?></td>
                            <td><?php echo $res['course_name']?></td>
                            <td><?php echo $res['subject_name']?></td>
                            
                            
                            
                            <td>
                              <a href="edit_student.php?id=<?php echo $res['id']?>" class="btn  btn-success">Edit</a>
                            </td>
                            <td>
                              <a href="delete.php?student&&id=<?php echo $res['id']?>" class="btn  btn-danger">Delete</a>
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
               
                <!-- End Table with stripped rows -->
  
              </div>
            </div>
  
          </div>
        </div>
      </section>

      


  </main><!-- End #main -->
    <script>
        async function myfun(id, status){
            fetch(`verify.php?stud&id=${id}&verify=${status}`)
            .then(res => res.text())
            .then(data => console.log(data));
        }
        
    </script>
  <!-- ======= Footer ======= -->
  <?php 
  include "include/footer.php";
 ?>

</body>

</html>