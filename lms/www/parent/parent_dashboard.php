<?php
session_start();
include "connect/db.php";
include "connect/fun.php";


$connect = new connect();
$fun=new fun($connect->dbconnect());

if (!isset($_SESSION['parent_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['parent_id'];
$attendance =  $fun->getTodayAttendance($student_id);
$result =  $fun->getstudname($student_id);



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard </title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <?php 
    include "include/links.php";
  ?>
</head>

<body>

  <!-- ======= Header ======= -->
    <?php 
      include "include/header.php";
    ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
    <?php 
      include "include/sideBar.php";
    ?>
  <!-- End Sidebar-->

  <main id="main" class="main">
      
      <div class="text-center mb-4">
    <img src="assets/img/logo.png" alt="DSA Logo" style="height: 60px;" onerror="this.style.display='none';">
    <h2 class="mt-2 mb-0" style="color:#0d6efd; font-weight:700;">DSA Academy</h2>
    <p class="text-muted">Digital Parent Access Panel</p>
  </div>

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
          <div class="col-lg-12">
  
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Welcome, Parent of Student : <?= $result['stud_name'] ?></h5>
                <h5 class="card-title"> Student ID: <?php echo $student_id ?></h5>
                  
                
                 <div class="row g-3">
  <div class="col-md-3">
    <div class="border rounded p-2 bg-light">
      <strong>Student ID:</strong><br>
      <?= htmlspecialchars($student_id) ?>
    </div>
  </div>
  <div class="col-md-3">
    <div class="border rounded p-2 bg-light">
      <strong>Date:</strong><br>
      <?= htmlspecialchars($attendance['date']) ?>
    </div>
  </div>
  <div class="col-md-3">
    <div class="border rounded p-2 bg-light">
      <strong>Entry Time:</strong><br>
      <?= htmlspecialchars($attendance['entry_time']) ?>
    </div>
  </div>
  <div class="col-md-3">
    <div class="border rounded p-2 bg-light">
      <strong>Exit Time:</strong><br>
      <?= htmlspecialchars($attendance['exit_time']) ?>
    </div>
  </div>

  <div class="col-md-3">
    <?php
      $status = strtolower(trim($attendance['status']));
    //   print_r($attendance);
      if ($status === 'present') {
        $badgeClass = 'bg-success text-white';
        $statusText = 'Present';
      } elseif ($status === 'absent') {
        $badgeClass = 'bg-danger text-white';
        $statusText = 'Absent';
      } else {
        $badgeClass = 'bg-warning text-dark';
        $statusText = 'Pending';
      }
    ?>
    <div class="border rounded p-2 <?= $badgeClass ?>">
      <strong>Status:</strong><br>
      <?= $statusText ?>
    </div>
  </div>
</div>



                
              </div>
            </div>
  
          </div>
        </div>
      </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
 <?php 
 include "include/footer.php";
 ?>
