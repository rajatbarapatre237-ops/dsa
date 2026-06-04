<?php
session_start();
include "connect/db.php";
include "connect/fun.php";

$connect = new connect();
$conn = $connect->dbconnect();

// Replace with dynamic student ID (e.g., from session) if needed
// $student_id = 214;
$student_id = $_SESSION['parent_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <?php include "include/links.php"; ?>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

  <?php include "include/header.php"; ?>
  <?php include "include/sideBar.php"; ?>

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
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Welcome, Parent of Student ID: <?php echo htmlspecialchars('DSA' . $student_id); ?></h5>

              <style>
  .month-buttons a {
    min-width: 120px;
    text-align: center;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
    border-radius: 12px;
  }

  .month-buttons a:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
  }
</style>

<div class="d-flex flex-wrap gap-3 justify-content-center month-buttons mt-3">
  <?php
    $currentYear = date("Y");
    $currentMonth = date("Y-m");

    for ($m = 1; $m <= 12; $m++) {
        $monthValue = $currentYear . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
        $monthLabel = date('F y', mktime(0, 0, 0, $m, 1, $currentYear)); // e.g., "June 24"

        $btnClass = ($monthValue === $currentMonth) ? 'btn-success' : 'btn-outline-primary';

        echo "<a href='view_attendance.php?month=$monthValue' class='btn $btnClass px-4 py-2'>$monthLabel</a>";
    }
  ?>
</div>

              </div>

            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include "include/footer.php"; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
