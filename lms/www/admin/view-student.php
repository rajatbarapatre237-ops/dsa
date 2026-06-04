<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());
  $courseee = $fun->fetchAllCourses();
 

  if(isset($_POST['submit']) ){
 
  
    $course = trim($_POST['course']);
    $fetch = $fun->fetchStudent($course);
   
  
    }else{
      $fetch = $fun->fetchAllStudents();

    }


if(isset($_POST['assign']) ){
 
  
      
      $fetch = $fun->fetchnobatchStudent();
     
    
      }
  
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Student</title>
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
        <h1>View Students</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Students</li>
            <li class="breadcrumb-item active">View Student</li>
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
                <h5 class="card-title">Student Table</h5>
                <form action="view-student.php" method="post">
              <div class="d-flex ">
                <select name="course" id="course" class="form-select w-25">
                            <option selected>select Course </option>
                            <?php 
                            if (mysqli_num_rows($courseee) > 0) {
                                while ($teach = mysqli_fetch_assoc($courseee)) {
                                    
                                ?>
                                <option value="<?php echo $teach['course_name'];?>" class=""><?php echo $teach['course_name'];?></option>
                            <?php 
                                }
                                }
                             
                            ?>
                            
                        </select>
                        <button type="submit" name="submit" class="btn btn-primary mx-5">Submit</button>
                        <button type="submit" name="assign" class="btn btn-warning mx-5 float-end">Assign Batch</button>
                      </div>
              </form>
                <!-- Table with stripped rows -->
                <div class="table-responsive">
                    <table class="table  datatable">
                        <thead>
                        
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">StId</th>
                            <th scope="col">StName</th>
                            <th scope="col">Age</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Course</th>
                            <th scope="col">Batch</th>
                            <th scope="col">Fees</th>
                            <th scope="col">Remaining Fees</th>
                            <th scope="col"  >Deposit</th>

                            <th scope="col">Verification</th>
                            <th scope="col">Test marks</th>
                            <th scope="col"  >Edit</th>
                            <th scope="col"  >Delete</th>
                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php 
                            if(mysqli_num_rows($fetch)>0){
                                $sr = 1;
                                while($res = mysqli_fetch_assoc($fetch)){
                                    
                                $deposit=  $res['course_fees'] - $res['balance_fees']
                                   // print_r($course_taken);

                                
                        ?>
                          <tr>
                            <th scope="row"><?php echo $sr?></th>
                            <td><?php echo "DSA".$res['id']?></td>
                            <td><?php echo $res['name']?></td>
                            <td><?php echo $res['age']?></td>
                            <td><?php echo $res['mobile']?></td>
                            <td><?php echo $res['course_name']?></td>
                            <td><?php echo $res['batch']?></td>
                            <td><?php echo $res['course_fees']?></td>
                            <td><?php echo $res['balance_fees']?></td>
                            <td><?php echo $deposit?></td>
                            
                            <td>
                                <div class="form-check form-switch">


                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" <?php echo ($res['status'])?("checked"):("")?> onclick="myfun(<?php echo $res['id'].','.$res['status']?>)">
                                      
                              </div>
                              
                            </td>
                            <!-- <td  class="d-inline">
                              <a href="studView.php?id=<?php echo $res['id'] ?>" class="btn w-auto btn-info">View</a>
                            </td> -->
                            <td>
                              <a href="student_class_test_marks.php?id=<?php echo (int) $res['id']; ?>" class="btn btn-info btn-sm">Marks</a>
                            </td>
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