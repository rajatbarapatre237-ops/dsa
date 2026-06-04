
<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect=new connect();
  $fun=new fun($connect->dbconnect());

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = mysqli_real_escape_string($connect->dbconnect(), $_POST['email']);
    $course = mysqli_real_escape_string($connect->dbconnect(), $_POST['course']);
    $subject = mysqli_real_escape_string($connect->dbconnect(), $_POST['subject']);

    // Validate the inputs
    if (!empty($email) && !empty($course) && !empty($subject)) {
        // Insert data into the database
        $fet = "INSERT INTO courses_subjects (teacher_email, course_name, subject_name) VALUES ('$email', '$course', '$subject')";

        if (mysqli_query($connect->dbconnect(), $fet)) {
            echo "<div class='alert alert-success'>Data inserted successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error inserting data: " . mysqli_error($connect->dbconnect()) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Please fill in all fields.</div>";
    }
}



// if(isset($_POST['submit']) ){
 
//   $email =trim($_POST['email']);

//   $course = trim($_POST['course']);
//   $fetch = $fun->addanothercourse($email,$course);

//   }

  

?>
                     
                    <!DOCTYPE html>
                    <html lang="en">
                    
                    <head>
                      <meta charset="utf-8">
                      <meta content="width=device-width, initial-scale=1.0" name="viewport">
                    
                      <title>Assign Subject to Teacher</title>
                      <meta content="" name="description">
                      <meta content="" name="keywords">
                      <?php 
                        include "include/links.php";
                      ?>
                      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <h1>Assign Subject to Teacher</h1>
                            <nav>
                              <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item">Teacher</li>
                                <li class="breadcrumb-item active">Add Another Course to Teacher </li>
                              </ol>
                            </nav>
                          </div>
 
 
 <section class="section">
        <div class="card">
            <div class="card-body">
              <h5 class="card-title">Assign Subject</h5>
                <?php 
                  if(isset($_POST['submit'])){
                ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                     <?php 
                  
                     if(isset($fet)){
                        
                       echo "Course Added!";
                     }
                      else{
                       
                      echo "Course already assigned!";
                     }
                     ?> 
                      <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                    </div>

                <?php    
                  }
                ?>
              <!-- No Labels Form -->
              <form class="row g-3" action="assign_subject.php" method="POST">
            <!-- Teacher Select -->
            <div class="col-md-6">
                <select name="email" id="email" class="form-select">
                    <option selected>Select Teacher</option>
                </select>
            </div>

            <!-- Course Select -->
            <div class="col-md-6">
                <select name="course" id="course" class="form-select" disabled>
                    <option selected>Select Course</option>
                </select>
            </div>

            <!-- Subject Select -->
            <div class="col-md-6">
                <select name="subject" id="subject" class="form-select" disabled>
                    <option selected>Select Subject</option>
                </select>
            </div>

            <!-- Submit and Reset Buttons -->
            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form><!-- End No Labels Form -->

            </div>
          </div>
      </section>
      <script>
        $(document).ready(function () {
    // Load Teachers
    $.ajax({
        url: 'data_handler.php',
        method: 'POST',
        data: { action: 'get_teachers' },
        success: function (data) {
            $('#email').append(data);
        }
    });

    // Load Courses based on Teacher selection
    $('#email').on('change', function () {
        var teacherEmail = $(this).val();
        if (teacherEmail) {
            $.ajax({
                url: 'data_handler.php',
                method: 'POST',
                data: {
                    action: 'get_courses',
                    email: teacherEmail
                },
                success: function (data) {
                    $('#course').html('<option selected disabled>Select Course</option>').append(data);
                    $('#course').prop('disabled', false);
                    $('#subject').html('<option selected disabled>Select Subject</option>').prop('disabled', true);
                }
            });
        }
    });

    // Load Subjects based on Course selection
    $('#course').on('change', function () {
        var courseName = $(this).val();
        if (courseName) {
            $.ajax({
                url: 'data_handler.php',
                method: 'POST',
                data: {
                    action: 'get_subjects',
                    course: courseName
                },
                success: function (data) {
                    $('#subject').html('<option selected disabled>Select Subject</option>').append(data);
                    $('#subject').prop('disabled', false);
                }
            });
        }
    });
});

    </script>
      <script>
        document.getElementById('date').valueAsDate = new Date();
    </script>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
<?php 
  include "include/footer.php";
?>

</body>

</html>