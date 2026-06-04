<?php 
	include "auth_session.php";
	include "connect/db.php";
	include "connect/fun.php";
	$connect = new connect();
	$fun = new fun($connect->dbconnect());

	$sid = $_SESSION['sid'];
	$id = substr($sid, 3);
	$transaction = $fun->getTransactionWithId($sid);
	$stud = $fun->getStudentByID($id);
	$student = mysqli_fetch_assoc($stud);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include "include/head.php"; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body, html {
			overflow-x: hidden;
		}
		.card {
			word-wrap: break-word;
		}
		.transaction-card {
			border-left: 5px solid #0d6efd;
			background: #f8f9fa;
		}
	</style>
</head>
<body>

	<?php include "include/sidebar.php"; ?>

	<div class="main-container container-fluid px-3 px-md-4" style="margin-top: -50px; overflow-x: hidden;">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Money Details</h4>
							</div>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index3.html">Home</a></li>
									<li class="breadcrumb-item active">Transaction</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<!-- Transaction Details -->
				<div class="invoice-wrap">
					<div class="invoice-box" style="overflow-x: hidden;">
						<h4 class="text-center mb-4 fw-bold">Transaction Details</h4>

						<div class="mb-4">
							<h6 class="mb-1">Student Name</h6>
							<p class="fw-bold text-primary"><?php echo $student['name']; ?></p>
						</div>

						<!-- Transactions -->
						<?php 
						$amount = 0;
						$date = "";
						if (mysqli_num_rows($transaction) > 0) {
							$sr = 1;
							while ($res = mysqli_fetch_assoc($transaction)) {
								$amount += $res['amount'];
								$date = $res['date'];
						?>
						<div class="card transaction-card shadow-sm mb-3">
							<div class="card-body">
								<div class="d-flex justify-content-between mb-2">
									<span class="fw-bold text-secondary">#<?php echo $sr; ?></span>
									<span class="fw-bold text-success">₹<?php echo $res['amount']; ?></span>
								</div>
								<p class="mb-1"><strong>Transaction ID:</strong> TAC<?php echo $res['id']; ?></p>
								<p class="mb-1"><strong>Course:</strong> <?php echo $res['reason']; ?></p>
								<p class="mb-0"><strong>Date:</strong> <?php echo $res['date']; ?></p>
							</div>
						</div>
						<?php $sr++; } } else { ?>
							<div class="alert alert-warning text-center">No Transaction Found</div>
						<?php } ?>

						<!-- Summary -->
						<div class="row mt-4">
							<div class="col-md-4 mb-3">
								<p class="mb-1">Account No: <strong>123 456 789</strong></p>
								<p class="mb-1">Subject: <strong>Fees</strong></p>
							</div>
							<div class="col-md-4 mb-3 text-center">
								<p class="mb-1 fw-semibold">Last Transaction</p>
								<p class="mb-0"><?php echo $date; ?></p>
							</div>
							<div class="col-md-4 mb-3 text-md-end">
								<p class="mb-0 fs-5 fw-bold text-danger">Total: ₹<?php echo $amount; ?>/-</p>
							</div>
						</div>

						<h5 class="text-center pt-4">Thank You!!</h5>
					</div>
				</div>

				<?php include "include/footer.php"; ?>
			</div>
		</div>
	</div>
</body>
</html>
