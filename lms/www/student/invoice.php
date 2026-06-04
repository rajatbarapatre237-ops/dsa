<?php
error_reporting(0);
include "auth_session.php";
include "connect/db.php";
include "connect/fun.php";
$connect=new connect();
$fun=new fun($connect->dbconnect());

$sid = substr($_SESSION['sid'],3);
$stud = $fun->getStudentByID($sid);
$student = mysqli_fetch_assoc($stud);
$courseTaken = $fun->fetchCoursesTaken($sid);
$courses = explode(",",$courseTaken['courses']);
$fees = explode(",",$courseTaken['course_fees']);

?>

<!DOCTYPE html>
<html>
<?php
include "include/head.php";
?>

<body>

	<?php
	include "include/sidebar.php";
	?>

	<div class="main-container" style="margin-top: -50px">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Studied</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item">
										<a href="index3.html">Home</a>
									</li>
									<li class="breadcrumb-item active" aria-current="page">
										Courses
									</li>
								</ol>
							</nav>
						</div>
						<div class="col-md-6 col-sm-12 text-right">
							<!--<div class="btn btn-primary">-->
							<!--	All Courses-->
							
							<!--</div>-->
						</div>
					</div>
				</div>
				<div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Student Details :-</h5>
							
							
                            <div class="text-center">
							<p><strong><?php echo $student['name'] ?></strong></p>
                            <p><?php echo $student['email'] ?></p>
                            <p>Total Courses: <strong><?php echo sizeof($courses) ?></strong></p>
                            <p>Joining Date: <strong><?php echo $student['date_of_joining'] ?></strong></p>
							</div>
							</div>
                    </div>

                    <div class="invoice-desc">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course Name</th>
                                    <th>Status</th>
                                    <th>Course Fees</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sr = 1;
                                foreach ($courses as $cid) {
                                    if ($sr == 0 && sizeof($courses) != 1) {
                                        $sr++;
                                        continue;
                                    } else if ($sr == sizeof($courses)) {
                                        $msg = "Current";
                                    } else {
                                        $msg = "Done";
                                    }
                                    $course = $fun->fetchCourseWithemail($student['email']);
                                    $course = mysqli_fetch_assoc($course);
                                ?>
                                    <tr>
                                        <td><?php echo $sr ?></td>
                                        <td><?php echo $course['course_name'] ?></td>
                                        <td><?php echo $msg ?></td>
                                        <td><?php echo $course['course_fees'] ?></td>
                                    </tr>
                                <?php 
                                    $sr++; 
                                }
                                ?>
                            </tbody>
                        </table>

                        <div class="invoice-desc-footer mt-4">
                            <div class="d-flex justify-content-between">
                                <div><strong>Course Name:</strong> <?php echo $student['course_name'] ?></div>
                                <div><strong>Remaining Fees:</strong> <span class="text-danger"><?php echo $student['balance_fees'] ?>/-</span></div>
                            </div>
                        </div>
                    </div>

                    <h4 class="text-center mt-4">Thank You!!</h4>
                </div>
            </div>
        </div>
    </div>
			<?php  
					include "include/footer.php";
				?>
	<!-- End Google Tag Manager (noscript) -->
</body>

</html>