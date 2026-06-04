<?php
include "connect/db.php";
include "connect/fun.php";

session_start();
$connect = new connect();
$fun = new fun($connect->dbconnect());
$email = $_SESSION['email'];
// Check if the session 'email' is set; if not, redirect to the login page
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit(); // Ensure no further code is executed
}
$fetch = $fun->fetchTeacherWithemail($email);
$cour = $fun->fetchCourseWithemail($email);



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php
    include "include/links.php";
    ?>
</head>

<body>
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
            <h1> Teachers Profile</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <!-- <li class="breadcrumb-item">Teachers</li>
            <li class="breadcrumb-item active">View Teachers</li> -->
                </ol>
            </nav>
        </div>
        <p class="text-center text-danger"><?php
        if (isset($_GET['msg'])) {
            echo $_GET['msg'];
        }
        ?></p>
        <div class="container mt-5">
            <div class="row">


                <div class="col-md-8">
                    <!-- Profile Details -->
                    <div class="card">
                        <div class="card-header">
                            <h4>Profile</h4>
                        </div>
                        <div class="card-body">
    <?php
    if (mysqli_num_rows($fetch) > 0) {
        $sr = 1;
        while ($res = mysqli_fetch_assoc($fetch)) {
            ?>
            <div class="mb-3 d-flex">
                <label for="name" class="form-label">Full Name:-</label>
                <p class="px-5"><?php echo $res['name']; ?></p>
            </div>
            <div class="mb-3 d-flex">
                <label for="email" class="form-label">Email:-</label>
                <p class="px-5"><?php echo $res['email']; ?></p>
            </div>
            <div class="mb-3 d-flex">
                <label for="phone" class="form-label">Phone:-</label>
                <p class="px-5"><?php echo $res['phone']; ?></p>
            </div>
            <div class="mb-3 d-flex">
                <label for="salary" class="form-label">Salary:-</label>
                <p class="px-5"><?php echo $res['salary']; ?></p>
            </div>
            <label for="courses" class="form-label">Courses:-</label>

            <div class="mb-3 d-flex">

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Course</th>
                            <th scope="col">Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <?php
    if (mysqli_num_rows($cour) > 0) {
        
        while ($sub = mysqli_fetch_assoc($cour)) {
            ?>
                            <td ><?php echo $sub['course_name'];?></td>
                            <td><?php echo $sub['subject_name'];?></td>
                            </tr>
                            <?php
           
        }
    }
    ?>
                    </tbody>
                </table>
            </div>
            <?php
            $sr++;
        }
    }
    ?>
</div>

                    </div>
                </div>

            </div>
        </div>
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <?php
    include "include/footer.php";
    ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>