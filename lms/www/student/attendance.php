<?php
// error_reporting(0);
include "auth_session.php";
include "connect/db.php";
include "connect/fun.php";
$connect=new connect();
$fun=new fun($connect->dbconnect());

$sid = substr($_SESSION['sid'],3);
 
    $attend = $fun->fetchAttendance($sid);

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
								<h4>Attendance</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item">
										<a href="index3.html">Home</a>
									</li>
									<li class="breadcrumb-item active" aria-current="page">
										Attendance Details
									</li>
								</ol>
							</nav>
						</div>
						<div class="col-md-6 col-sm-12 text-right">
							
						</div>
					</div>
				</div>
				<div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Attendance Details :-</h5>
							
							
                            
							</div>
                    </div>

                    <div class="invoice-desc">
                       <div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">Student Id</th>
                <th scope="col">Course</th>
                <th scope="col">Batch</th>
                <th scope="col">Total</th>
                <th scope="col">Present</th>
                <th scope="col">Absent</th>
                <th scope="col">Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($attend) > 0) {
                while ($teach = mysqli_fetch_assoc($attend)) {
            ?>
            <tr>
                <th scope="row">
                    <?php echo "DSA" . $teach['sid']; ?>
                </th>
                <td><?php echo $teach['course']; ?></td>
                <td><?php echo $teach['batch']; ?></td>
                <td><?php echo $teach['total_days']; ?></td>
                <td><?php echo $teach['present_days']; ?></td>
                <td><?php echo $teach['absent_days']; ?></td>
                <td><?php echo $teach['attendance_percentage'] . "%"; ?></td>
            </tr>
            <?php 
                }
            }
            ?>
        </tbody>
    </table>
</div>

                        <div class="invoice-desc-footer mt-4">
                           
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