
<?php
  
//  private $username = "acesdin_dsa";
//     private $password = "acesdin_dsa";
//     private $dbname = "acesdin_dsa";



session_start();

require_once __DIR__ . '/../admin/connect/db.php';
$dbConnect = new connect();
$conn = $dbConnect->dbconnect();

if (!$conn || $conn->connect_error) {
    die('Connection failed: ' . ($conn ? $conn->connect_error : 'unknown'));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Query to check if user exists
        $sql = "SELECT * FROM teacher WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password (assuming plain-text passwords for simplicity; use hashing in production)
            if ($password === $user['password']) {
                // Clear other panels' session keys so API checks (e.g. class tests) don't think we're still admin/student/parent
                unset(
                    $_SESSION['username'],
                    $_SESSION['is_valid'],
                    $_SESSION['sid'],
                    $_SESSION['is_start'],
                    $_SESSION['parent_id']
                );
                $_SESSION['email'] = $user['email'];
                echo "Login successful! Redirecting...";
                // Redirect to a secure page
                header("Location:dashboard.php");
                exit();
            } else {
                echo "Invalid password!";
            }
        } else {
            echo "No account found with this email!";
        }
    } else {
        echo "Please fill in all fields!";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from dreamslms.dreamstechnologies.com/html/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 15 Nov 2024 07:42:17 GMT -->
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>Teacher Login</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/favicon.svg">

	<!-- Theme Settings Js -->
	<script src="assets/js/theme-script.js" type="9436fb567d6dafd9d995238a-text/javascript"></script>

	<!-- Boots../trap CSS -->
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">

	<!-- Owl Carousel CSS -->
	<link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">

	<!-- Feathericon CSS -->
	<link rel="stylesheet" href="../assets/plugins/feather/feather.css">

	<!-- Main CSS -->
	<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

	<!-- Main Wrapper -->
	<div class="main-wrapper log-wrap">

		<div class="row">

			<!-- Login Banner -->
			<div class="col-md-6 login-bg">
				<div class="owl-carousel login-slide owl-theme">
					
					<div class="welcome-login">
						<div class="login-banner">
							<img src="assets/img/login-img.png" class="img-fluid" alt="Logo">
						</div>
						<div class="mentor-course text-center">
							<h2>Welcome to <br>DSA</h2>
							<p>At DSA, we are committed to empowering individuals to unlock their full potential and achieve their
                personal and professional goals. Our coaching services are designed to provide tailored guidance,
                support, and tools to help you navigate challenges, enhance your skills, and cultivate a mindset for
                success.</p>
						</div>
					</div>
				</div>
			</div>
			<!-- /Login Banner -->

			<div class="col-md-6 login-wrap-bg">

				<!-- Login -->
				<div class="login-wrapper">
					<div class="loginbox">
						<div class="w-100">
							<div class="img-logo">
								<img src="assets/img/Screenshot_2024-11-21_133923-transformed-removebg-preview.png" class="img-fluid" alt="Logo">
								<div class="back-home">
									<a href="../index.php">Back to Home</a>
									<span class="px-4">|</span>
									<a href="../student/login.php">Student Login</a>
								</div>
							</div>
							<h1>Sign into Your Account</h1>
							<form action="index.php" method="post">
								<div class="input-block">
									<label class="form-control-label">Email</label>
									<input type="email" name="email" class="form-control" placeholder="Enter your email address">
								</div>
								<div class="input-block">
									<label class="form-control-label">Password</label>
									<div class="pass-group">
										<input type="password" name="password" class="form-control pass-input"
											placeholder="Enter your password">
										<span class="feather-eye toggle-password"></span>
									</div>
								</div>
								<div class="forgot">
									<span><a class="forgot-link" href="forgot-password.html">Forgot Password
											?</a></span>
								</div>
								<div class="remember-me">
									<label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> Remember me
										<input type="checkbox" name="radio">
										<span class="checkmark"></span>
									</label>
								</div>
								<div class="d-grid">
									<button class="btn btn-primary btn-start" name="submit" type="submit">Sign In</button>
								</div>
							</form>
						</div>
						<!-- <div class="google-bg text-center">
							<span><a href="#">Or sign in with</a></span>
							
							<p class="mb-0">New User ? <a href="register.php">Create an Account</a></p>
						</div> -->
					</div>
				</div>
				<!-- /Login -->

			</div>

		</div>

	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="../assets/js/jquery-3.7.1.min.js" type="9436fb567d6dafd9d995238a-text/javascript"></script>

	<!-- Bootstrap Core JS -->
	<script src="../assets/js/bootstrap.bundle.min.js" type="9436fb567d6dafd9d995238a-text/javascript"></script>

	<!-- Owl Carousel -->
	<script src="../assets/js/owl.carousel.min.js" type="9436fb567d6dafd9d995238a-text/javascript"></script>

	<!-- Custom JS -->
	<script src="../assets/js/script.js" type="9436fb567d6dafd9d995238a-text/javascript"></script>

<script src="../assets/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="9436fb567d6dafd9d995238a-|49" defer></script></body>


<!-- Mirrored from dreamslms.dreamstechnologies.com/html/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 15 Nov 2024 07:42:19 GMT -->
</html>