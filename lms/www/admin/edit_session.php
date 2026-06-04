<?php
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: view_sessions.php?msg=' . urlencode('Invalid session'));
    exit;
}

$res = $fun->getAcademicSessionById($id);
$session = $res ? mysqli_fetch_assoc($res) : null;
if (!$session) {
    header('Location: view_sessions.php?msg=' . urlencode('Session not found'));
    exit;
}

$message = '';
if (isset($_POST['submit'])) {
    $name = trim((string) ($_POST['session_name'] ?? ''));
    if ($name === '') {
        $message = 'Please enter a session name (e.g. 2025-26).';
    } elseif ($fun->updateAcademicSession($id, $name)) {
        header('Location: view_sessions.php?msg=' . urlencode('Session updated'));
        exit;
    } else {
        $message = 'Could not update session. The name may already exist.';
    }
    $session['session_name'] = $name;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Edit Session</title>
  <?php include "include/links.php"; ?>
</head>
<body>
<?php include "include/header.php"; ?>
<?php include "include/sideBar.php"; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Edit Session</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="view_sessions.php">Sessions</a></li>
        <li class="breadcrumb-item active">Edit Session</li>
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
        <p class="text-muted small">Renaming updates all students linked to this session.</p>
        <form class="row g-3" action="edit_session.php?id=<?php echo $id; ?>" method="POST">
          <div class="col-md-12">
            <label class="form-label">Session name</label>
            <input type="text" class="form-control" name="session_name"
              value="<?php echo htmlspecialchars($session['session_name']); ?>"
              placeholder="e.g. 2025-26" required>
          </div>
          <div class="text-center">
            <button type="submit" name="submit" class="btn btn-primary">Save changes</button>
            <a href="view_sessions.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include "include/footer.php"; ?>
</body>
</html>
