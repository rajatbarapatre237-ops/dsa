<?php
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$message = '';
if (isset($_POST['submit'])) {
    $name = trim((string) ($_POST['session_name'] ?? ''));
    if ($name === '') {
        $message = 'Please enter a session name (e.g. 2025-26).';
    } elseif ($fun->insertAcademicSession($name)) {
        header('Location: view_sessions.php?msg=Session added');
        exit;
    } else {
        $message = 'Could not add session. It may already exist.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Add Session</title>
  <?php include "include/links.php"; ?>
</head>
<body>
<?php include "include/header.php"; ?>
<?php include "include/sideBar.php"; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Add Session</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item">Sessions</li>
        <li class="breadcrumb-item active">Add Session</li>
      </ol>
    </nav>
  </div>

  <?php if ($message !== '') { ?>
    <p class="text-center text-danger"><?php echo htmlspecialchars($message); ?></p>
  <?php } ?>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Academic session</h5>
        <p class="text-muted small">Use a label like <strong>2025-26</strong>. This appears when adding students, attendance, and class test marks.</p>
        <form class="row g-3" action="add_session.php" method="POST">
          <div class="col-md-12">
            <label class="form-label">Session name</label>
            <input type="text" class="form-control" name="session_name" placeholder="e.g. 2025-26" required>
          </div>
          <div class="text-center">
            <button type="submit" name="submit" class="btn btn-primary">Add session</button>
            <a href="view_sessions.php" class="btn btn-secondary">View all sessions</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include "include/footer.php"; ?>
</body>
</html>
