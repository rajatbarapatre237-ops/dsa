 <?php 
  // include "connect/db.php";
  // include "connect/fun.php";
  

  // $connect = new connect();
  // $fun=new fun($connect->dbconnect());

 
  // if(isset($_POST['submit'])){
  //   $id = $_POST['id'];
  //   $password = $_POST['password'];
   
  //   $result = $fun->parentlogin($id,$password);
    
  // }

  
?>
<!--<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Parent Panel - DSA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
      <h4 class="text-center mb-4">Parent Panel Login</h4>
      <form action="" method="post">
        <div class="mb-3">
          <label for="email" class="form-label">Student ID</label>
          <input type="text" class="form-control" id="email" name="id" placeholder="Student ID" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
        </div>
        <div class="mb-3 form-check">
  <input type="checkbox" class="form-check-input" id="remember" name="remember">
  <label class="form-check-label" for="remember">Remember Me</label>
</div>
        
        <button type="submit" name="submit" class="btn btn-primary w-100">Sign In</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> -->



<?php 
  include "connect/db.php";
  include "connect/fun.php";

  session_start();
  if (isset($_SESSION['parent_id'])) {
    header("Location: parent_dashboard.php");
    exit();
  }

  $connect = new connect();
  $fun = new fun($connect->dbconnect());

  // Auto-login from cookie
  if (isset($_COOKIE['remember_parent'])) {
    $id = $_COOKIE['remember_parent'];
    $default_password = "123456";
    $result = $fun->parentlogin($id, $default_password);

    if ($result) {
      unset($_SESSION['username'], $_SESSION['is_valid'], $_SESSION['email'], $_SESSION['sid'], $_SESSION['is_start']);
      $_SESSION['parent_id'] = $id;
      header("Location: parent_dashboard.php");
      exit();
    }
  }

  if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $password = $_POST['password'];

    $result = $fun->parentlogin($id, $password);

    if ($result) {
      unset($_SESSION['username'], $_SESSION['is_valid'], $_SESSION['email'], $_SESSION['sid'], $_SESSION['is_start']);
      $_SESSION['parent_id'] = $id;

      if (isset($_POST['remember'])) {
        setcookie("remember_parent", $id, time() + (86400 * 30), "/"); // 30 days
      }

      header("Location: parent_dashboard.php");
      exit();
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Parent Panel - DSA</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .branding h1 {
      font-weight: 700;
      font-size: 2rem;
      color: #0d6efd;
    }
    .branding img {
      height: 60px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body class="bg-light">

  <div class="container d-flex justify-content-center align-items-center flex-column" style="min-height: 100vh;">
    
    <!-- Branding Section -->
    <div class="branding text-center mb-4">
      <!-- Use your actual logo path here -->
      <img src="assets/img/logo.png" alt="DSA Logo" onerror="this.style.display='none';" />
      <h1>DSA Academy</h1>
      <p class="text-muted mb-0">Digital Parent Access Panel</p>
    </div>

    <!-- Login Card -->
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
      <h4 class="text-center mb-4">Parent Panel Login</h4>
      <form action="" method="post">
        <div class="mb-3">
          <label for="email" class="form-label">Student ID</label>
          <input type="text" class="form-control" id="email" name="id" placeholder="Student ID" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="********" required>
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="remember" name="remember" checked>
          <label class="form-check-label" for="remember">Remember Me</label>
        </div>
        <button type="submit" name="submit" class="btn btn-primary w-100">Sign In</button>
      </form>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
