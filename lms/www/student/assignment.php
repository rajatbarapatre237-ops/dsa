<?php
include "connect/db.php";
include "connect/fun.php";
include "auth_session.php";

$connect=new connect();
$fun=new fun($connect->dbconnect());
$sid = $_SESSION['sid'];
$sid = substr($sid,3);
$assignment = $fun->fetchAssignment($sid);
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
								<h4>Question Table</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item">
										<a href="3index.html">Home</a>
									</li>
									<li class="breadcrumb-item active" aria-current="page">
										Assignment
									</li>
								</ol>
							</nav>
						</div>
						
					</div>
				</div>
				<!-- Simple Datatable start -->
				<div class="card-box mb-30">
					
					<div class="pb-20 table-responsive">
						<table class="data-table table stripe hover nowrap">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#</th>
									<th>Document Name</th>
									<th>Document Type</th>
									<th>File</th>
									
								</tr>
							</thead>
							<tbody>
								<?php 
									if(mysqli_num_rows($assignment)>0){
										$sr = 1;
										while($res = mysqli_fetch_assoc($assignment)){
								?>

								
										<tr>
											<td class="table-plus"><?php echo $sr?></td>
											
                      						<td><?php echo $res['document_name'];?></td>
                      						<td><?php echo $res['type'];?></td>
											<?php 
												if($res['type'] == 'file'){
											?>
												<td> <a href="../admin/documents/<?php echo $res['document']?>" target="_blank"> Click here</a></td>

											<?php		
												}
												else{

											?>
												<td> <a href="<?php echo $res['document']?>" target="_blank"> Click here</a></td>

													
											<?php
												}
											?>
											
										</tr>
								<?php		$sr++;	
										}
									}
								?>
								
							</tbody>
						</table>
					</div>
				</div>
				<!-- Simple Datatable End -->

				<!-- multiple select row Datatable End -->
				<!-- Checkbox select Datatable start -->

				<!-- Checkbox select Datatable End -->
				<!-- Export Datatable start -->

				<!-- Export Datatable End -->
			</div>
			<?php  
					include "include/footer.php";
				?>
	<!-- End Google Tag Manager (noscript) -->
</body>

</html>