<?php 
	include "auth_session.php";
	include "connect/db.php";
	include "connect/fun.php";
	$connect=new connect();
	$fun=new fun($connect->dbconnect());
	
	

?>

<!DOCTYPE html>
<html>
	<?php 
		include "include/head.php";
	?>
	<body>
		<style type="text/css">
		a{
		    text-decoration: none;
		}
		a:hover{
		    text-decoration: none;
		}
			 .center {
      height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
    
    }
    .form-input {
    height: 192px;
      width:192px;
      padding:20px;
      
      
    }
    .form-input input {
      display:none;
    
    }
    .form-input label {
      display:block;
      width:45%;
      height:40px;

      margin-left: 25%;
      line-height:50px;
      text-align:center;
      background:#1172c2;
    
      color:#fff;
      font-size:15px;
      font-family:"Open Sans",sans-serif;
      text-transform:uppercase;
      font-weight:600;
      border-radius:5px;
      cursor:pointer;
    }
    
    .form-input img {
      width:100%;
      display:none;
    
      margin-bottom:30px;
    }

    @import url("https://fonts.googleapis.com/css?family=Lato:300");


/*** <--- CIRCLE STYLES ---> ***/


.circle {
  position: relative;
  width: 200px;
  height: 200px;
  margin: 0.5rem;
  border-radius: 50%;
  background: #FFCDB2;
  overflow: hidden;
}
.circle.per-25 {
  background-image: conic-gradient(#B5838D 70%, #FFCDB2 0);
}

.circle .inner {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 115px;
  height: 115px;
  background: #000;
  border-radius: 50%;
  font-size: 1.85em;
  font-weight: 300;
  color: rgba(255, 255, 255, 0.75);
}
		</style>

		<div class="pre-loader">
			<div class="pre-loader-box">
				<div class="loader-logo">
					<img src="assets/img/logoace.png" alt="DSA" />
				</div>
				<div class="loader-progress" id="progress_div">
					<div class="bar" id="bar1"></div>
				</div>
				<div class="percent" id="percent1">0%</div>
				<div class="loading-text">Loading...</div>
			</div>
		</div>
	
		

		<?php 
			include "include/sidebar.php";
			$sid = $_SESSION['sid'];
			$id = substr($sid,3);
			$student = $fun->getStudentByID($id);
			
			$stud = mysqli_fetch_assoc($student);
			echo "$sid";
			
		?>

		<div class="main-container" style="margin-top: -50px">
			<div class="pd-ltr-20">
				<div class="card-box pd-20 height-100-p mb-30">
					<div class="row align-items-center">
						<div class="col-md-4">
											
							<img id="" src="../admin/student_pfp/<?php echo $stud['pfp']?>" class="image image-fluid rounded w-50 h-50" alt="pfp">
						</div>
						<div class="col-md-8">
							<h4 class="font-20 weight-500 mb-10 text-capitalize">
							
								<div class="weight-600 font-30 text-blue"><?php echo $stud['name']?></div>
							</h4>
							<p class="font-18 max-width-600">
								
								<span>Phone no. : </span><span><?php echo $stud['mobile']?></span><br>
								<span>College: </span><span><?php echo $stud['school_name']?></span>
							</p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-3 mb-30">
						<div class="card-box height-100-p widget-style1">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									<div><img src="assets/img/profile.png"></div>
								</div>
								<div class="widget-data">
									<div class="h4 mb-0">User Id</div>
									<div class="weight-600 font-14"><?php echo "DSA".$stud['id']?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 mb-30">
						<div class="card-box height-100-p widget-style1">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									<div><img src="assets/img/phone.png"></div>
								</div>
								<div class="widget-data">
									<div class="h4 mb-0">Phone</div>
									<div class="weight-600 font-14"><?php echo $stud['mobile']?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 mb-30">
						<div class="card-box height-100-p widget-style1">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									<div ><img src="assets/img/age.png"></div>
								</div>
								<div class="widget-data">
									<div class="h4 mb-0">Age</div>
									<div class="weight-600 font-14"><?php echo $stud['age']?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 mb-30">
						<div class="card-box height-100-p widget-style1">
							<div class="d-flex flex-wrap align-items-center">
								<div class="progress-data">
									<div><img src="assets/img/univ.png"></div>
								</div>
								<div class="widget-data">
									<div class="h4 mb-0">College</div>
									<div class="weight-600 font-14"><?php echo $stud['school_name']?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--  -->
				
				<?php  
					include "include/footer.php";
				?>
		<!-- End Google Tag Manager (noscript) -->
<script>
    
        /*  ==========================================
        SHOW UPLOADED IMAGE
    * ========================================== */
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
    
            reader.onload = function (e) {
                $('#imageResult')
                    .attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $(function () {
        $('#upload').on('change', function () {
            readURL(input);
        });
    });
    
    /*  ==========================================
        SHOW UPLOADED IMAGE NAME
    * ========================================== */
    var input = document.getElementById( 'upload' );
    var infoArea = document.getElementById( 'upload-label' );
    
    input.addEventListener( 'change', showFileName );
    function showFileName( event ) {
      var input = event.srcElement;
      var fileName = input.files[0].name;
      infoArea.textContent = 'File name: ' + fileName;
    }
        
</script>
	</body>
</html>
