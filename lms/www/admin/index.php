<?php 
 session_start();
    include "connect/db.php";        
    include 'connect/fun.php';
    //include "include/auth_session.php";
    $connect=new connect();
    $fun=new fun($connect->dbconnect());
    if(isset($_POST['submit'])){
      $email = $_POST['email'];
      $password = $_POST['password'];
      [$msg, $flag] = $fun->login($email,$password);
      if($flag){
      //echo $flag.",".$email;
       
        $_SESSION['username'] = $email;
        $_SESSION['is_valid'] = true;
      //print_r($_SESSION);
        //echo "Passed";
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: dashboard.php");
      }
      else{
          echo "Failed";
      }
      
    }



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <?php 
    include "include/links.php";
  ?>
</head>
<style>
  .divider:after,
  .divider:before {
    content: "";
    flex: 1;
    height: 1px;
    background: #eee;
  }
</style>
<body>
    <section class="vh-100">
      <div class="container py-5 h-100">
        <div class="row d-flex align-items-center justify-content-center h-100">
          <div class="col-md-8 col-lg-7 col-xl-6">
            <img src="assets/img/login.svg"
              class="img-fluid" alt="Phone image">
          </div>
          <div class="col-md-7 col-lg-5 col-xl-5 offset-xl-1">
            <form action="index.php" method="POST">
              <!-- Email input -->
              <div class="form-outline mb-4">
                <input type="email" name="email" id="form1Example13" class="form-control form-control-lg" />
                <label class="form-label" for="form1Example13">Email address</label>
              </div>

              <!-- Password input -->
              <div class="form-outline mb-4">
                <input type="password" name="password" id="password" class="form-control form-control-lg" />
                <label class="form-label" for="password">Password</label>
              </div>
            <div class="form-check">
              <input class="form-check-input" name="form1Example1" type="checkbox"  id="form1Example1"  onclick="myFunction()" />
              <label class="form-check-label" for="form1Example1">Show Password </label>
            </div>
              <div class="d-flex justify-content-around align-items-center mb-4">
                <!-- Checkbox -->
                <div class="form-check">
                  <input class="form-check-input" name="remember" type="checkbox" value="" id="form1Example3" checked />
                  <label class="form-check-label" for="form1Example3"> Remember me </label>
                </div>
                <a href="#!">Forgot password?</a>
              </div>

              <!-- Submit button -->
              <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>

              <div class="divider d-flex align-items-center my-4">
                <p class="text-center fw-bold mx-3 mb-0 text-muted">OR</p>
              </div>

              <a class="btn btn-primary btn-lg btn-block" style="background-color: #3b5998" href="#!"
                role="button">
                <i class="fab fa-facebook-f me-2"></i>Continue with Facebook
              </a>
              <a class="btn btn-primary btn-lg btn-block" style="background-color: #55acee" href="#!"
                role="button">
                <i class="fab fa-twitter me-2"></i>Continue with Twitter</a>

            </form>
          </div>
        </div>
      </div>
    </section>
    
    <script>
        
        function myFunction() {
                  var x = document.getElementById("password");
                  if (x.type === "password") {
                    x.type = "text";
                  } else {
                    x.type = "password";
                  }
                }
    </script>
  <!-- ======= Footer ======= -->
 <?php 
 include "include/footer.php";
 ?>

</body>

</html>