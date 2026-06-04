<?php
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());
//echo $_GET['id'];
if(isset($_POST['submit'])){
    $fetch = $fun->updateStudentDetail($_POST);
    if($fetch){
        $msg = "Updated";
        header("Location: view-student.php?msg=$msg");
    }
    else{
        $msg = "Some Error Ocurred!";
        header("Location: view-student.php?msg=$msg");

    }
}
if(isset($_GET['id'])){

    $stud = $fun->getStudentByID($_GET['id']);
    $c=$fun->getCourseDetails();
    $b = $fun->getAllBatches();
    $sessions = $fun->getAcademicSessions();
    if($stud){

        $student = mysqli_fetch_assoc($stud);
    }
    else{
        $msg = "Student Not Found";
        header("Location: view-student.php?msg=$msg");
    }
}
else{
   
    header("Location: view-student.php");

}



//print_r($course);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Edit Student</title>
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
    ?><!-- End Header -->

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
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add Student Form</h5>

              <!-- No Labels Form -->
              <form class="row g-3"  action="edit_student.php" method="POST" enctype="multipart/form-data">
                <div class="col-md-12">
                  <input type="text" class="form-control" name="name" placeholder="Student name" value="<?php echo $student['name']?>">
                  <input type="text" class="form-control" name="id" placeholder="Enter Id" value="<?php echo $student['id']?>" hidden>
                </div>
                
                <div class="col-md-6">
                  <input type="text" class="form-control" name="age" placeholder="Enter Age" value="<?php echo $student['age']?>">
                </div>
                <div class="col-md-6">
                  <input type="text" class="form-control" name="email" placeholder="Enter Email" value="<?php echo $student['email']?>">
                </div>
                <div class="col-md-6">
                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile No. " value="<?php echo $student['mobile']?>" required />
                </div>
                
                    <div class="col-md-12">
                        <input type="text" name="school" id="school" class="form-control" placeholder="College Name " value="<?php echo $student['school_name']?>" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Session</label>
                        <select name="session" id="session" class="form-select">
                            <option value="">No session</option>
                            <?php
                            $currentSession = (string) ($student['session_name'] ?? '');
                            $activeSessions = [];
                            if ($sessions) {
                                while ($sess = mysqli_fetch_assoc($sessions)) {
                                    $activeSessions[$sess['session_name']] = true;
                                }
                            }
                            foreach (array_keys($activeSessions) as $sn) {
                                $selected = ($sn === $currentSession) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo htmlspecialchars($sn); ?>" <?php echo $selected; ?>>
                                        <?php echo htmlspecialchars($sn); ?>
                                    </option>
                                <?php
                            }
                            if ($currentSession !== '' && !isset($activeSessions[$currentSession])) {
                                ?>
                                    <option value="<?php echo htmlspecialchars($currentSession); ?>" selected>
                                        <?php echo htmlspecialchars($currentSession); ?> (inactive)
                                    </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course</label>
                        <select name="course" id="course" class="form-select">
                            <option selected>select Course </option>
                            <?php 
                            if (mysqli_num_rows($c) > 0) {
                                while ($res = mysqli_fetch_assoc($c)) {
                                    if($res['status']){
                                ?>
                                <option value="<?php echo $res['id'];?>" class="" <?php echo ($res['course_name'] == $student['course_name'])?("selected"):("")?>><?php echo $res['course_name'];?></option>
                            <?php }
                                }
                                }
                            else{
                                echo "<option value='no courses'>No courses available</option>";
                            }    
                            ?>
                            
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Batch</label>
                        <select name="batch" id="batch" class=" form-select">
                            <option selected>select Batch</option>
                            <?php 
                            if (mysqli_num_rows($b) > 0) {
                                while ($res = mysqli_fetch_assoc($b)) {
                                    if($res['status']){
                                ?>
                                <option value="<?php echo $res['name'];?>"  class="" <?php echo ($res['name'] == $student['batch'])?("selected"):("")?> ><?php echo $res['name']."  ".$res['start_time']." - ".$res['end_time']." ";?></option>
                            <?php }
                                }
                                }
                            else{
                                echo "<option value='no courses'>No Batches available</option>";
                            }    
                            ?>
                            
                        </select>
                    </div>
                
                
                <div class="col-md-6">
                    <input type="text"  name="fees" id="fees" class="form-control" placeholder="Fees"  value="<?php echo $student['course_fees']?>" required />
                </div>
                <div class="col-md-12">
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address " value="<?php echo $student['address']?>" required />
                    </div>
                <div class="col-md-6">
                    <input type="text" name="aadhar" id="aadhar" class="form-control"  value="<?php echo $student['aadhar']?>" placeholder="Aadhar Id" />
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date of Joining</label>
                    <input type="date" name="date" id="date" class="form-control"  value="<?php echo $student['date_of_joining']?>" placeholder="Date of Joining" required />
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control"  value="<?php echo $student['dob']?>" placeholder="Date of Birth" required />
                </div>
                
                <div class="col-md-12 text-center">
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