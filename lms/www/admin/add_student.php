<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  if(isset($_POST['submit'])){
    
      $query = $fun->insertStudentDetails($_POST,$_FILES);
      try {
                            
        if($query){
           // echo "<p class='m-10'>Added!!</p>";
         
        }
        else{
            throw new Exception("Message:");
        }
      }
      
      //catch exception
      catch(Exception $e) {
         //echo "<p class='text-2xl mb-6 mt-0 ml-10 font-bold'>Course already available</p>";
      }
  }
  else{
    
    $fetch = 0;
  }
  

?>
 <?php 
                        
    $c = $fun->getCourseDetails();
    $sessions = $fun->getAcademicSessions();

 ?> 

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add Student</title>
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
        <h1>Add Student</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Student</li>
            <li class="breadcrumb-item active">Add Student</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add Student Form</h5>

              <!-- No Labels Form -->
              <form class="row g-3"  action="add_student.php" method="POST" enctype="multipart/form-data">
                <div class="col-md-12">
                  <input type="text" class="form-control" name="name" placeholder="Student name">
                </div>
                <div class="col-md-12">
                  <label for="pfp">Upload Profile</label>
                  <input type="file" class="form-control" accept="image/*" name="pfp" placeholder="Profile Picture">
                </div>
                <div class="col-md-6">
                  <input type="text" class="form-control" name="age" placeholder="Enter Age">
                </div>
                <div class="col-md-6">
                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile No. " required />
                </div>
                <div class="col-md-6">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email Id "  />
                </div>
                <div class="col-md-6">
                    <input type="text" name="city" id="city" class="form-control" placeholder="Enter City " required />
                </div>
                <div class="col-md-6">
                    <input type="text" name="state" id="state" class="form-control" placeholder="Enter State " required />
                </div>
                
                
                    <div class="col-md-12">
                        <input type="text" name="school" id="school" class="form-control" placeholder="College Name " required />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Session</label>
                        <select name="session" id="session" class="form-select" required>
                            <option value="">Select session</option>
                            <?php
                            if ($sessions && mysqli_num_rows($sessions) > 0) {
                                while ($sess = mysqli_fetch_assoc($sessions)) {
                                    ?>
                                    <option value="<?php echo htmlspecialchars($sess['session_name']); ?>">
                                        <?php echo htmlspecialchars($sess['session_name']); ?>
                                    </option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Course</label>
                        <select name="course" id="course" class="form-select" required>
                            <option value="">Select course</option>
                            <?php 
                            if (mysqli_num_rows($c) > 0) {
                                while ($res = mysqli_fetch_assoc($c)) {
                                    if($res['status']){
                                ?>
                                <option value="<?php echo htmlspecialchars($res['course_name']); ?>">
                                    <?php echo htmlspecialchars($res['course_name']); ?>
                                </option>
                            <?php }
                                }
                            } else {
                                echo "<option value=''>No courses available</option>";
                            }    
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Batch</label>
                        <select name="batch" id="batch" class="form-select" required disabled>
                            <option value="">Select course first</option>
                        </select>
                    </div>
                
                
                <div class="col-md-6">
                    <input type="text"  name="fees" id="fees" class="form-control" placeholder="Fees" />
                </div>
                <div class="col-md-6">
                    <input type="text" name="aadhar" id="aadhar" class="form-control" placeholder="Aadhar Id" />
                </div>
                 <div class="col-md-12">
                  <input type="text" class="form-control" name="address" placeholder="Address">
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date of Joining</label>
                    <input type="date" name="date" id="date" class="form-control" value="" placeholder="Date of Joining" required />
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label">Date of Birth</label>
                    <input type="date" name="dob" id="date" class="form-control" value="" placeholder="Date of Birth" required />
                </div>
                
                
                <div class="text-center">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
                
              </form><!-- End No Labels Form -->

            </div>
          </div>
      </section>

    <script>
        document.getElementById('date').valueAsDate = new Date();
        (function () {
            var courseEl = document.getElementById('course');
            var batchEl = document.getElementById('batch');
            courseEl.addEventListener('change', function () {
                var course = courseEl.value;
                batchEl.innerHTML = '<option value="">Loading...</option>';
                batchEl.disabled = true;
                if (!course) {
                    batchEl.innerHTML = '<option value="">Select course first</option>';
                    return;
                }
                fetch('get_batches_by_course.php?course=' + encodeURIComponent(course), { credentials: 'same-origin' })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        batchEl.innerHTML = '<option value="">Select batch</option>';
                        if (data.ok && data.batches.length) {
                            data.batches.forEach(function (b) {
                                var o = document.createElement('option');
                                o.value = b.name;
                                o.textContent = b.label;
                                batchEl.appendChild(o);
                            });
                            batchEl.disabled = false;
                        } else {
                            batchEl.innerHTML = '<option value="">No batches for this course</option>';
                        }
                    })
                    .catch(function () {
                        batchEl.innerHTML = '<option value="">Error loading batches</option>';
                    });
            });
        })();
    </script>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
<?php 
  include "include/footer.php";
?>

</body>

</html>