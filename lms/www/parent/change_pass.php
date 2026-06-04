<?php
session_start();
include "connect/db.php";

// Check if parent is logged in
if (!isset($_SESSION['parent_id'])) {
    header("Location: index.php");
    exit();
}

// Connect to DB using your connect class
$connect = new connect();
$conn = $connect->dbconnect();

$parent_id = $_SESSION['parent_id'];
$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Fetch current password from DB
    $stmt = $conn->prepare("SELECT pass FROM parent WHERE id = ?");
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $stmt->bind_result($db_pass);
    $stmt->fetch();
    $stmt->close();

    if ($current === $db_pass) {
        if ($new === $confirm) {
            $update = $conn->prepare("UPDATE parent SET pass = ? WHERE id = ?");
            $update->bind_param("si", $new, $parent_id);
            if ($update->execute()) {
                $success = "Password updated successfully.";
            } else {
                $error = "Failed to update password.";
            }
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Change Password</title>
  <?php include "include/links.php"; ?>
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
    <h1>Change Password</h1>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-6 offset-lg-3">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Set a New Password</h5>

            <?php if ($success): ?>
              <div class="alert alert-success"><?= $success ?></div>
            <?php elseif ($error): ?>
              <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
              <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="text" name="current_password" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
              </div>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" onclick="togglePasswords()" id="showPasswords">
                <label class="form-check-label" for="showPasswords">Show New & Confirm Password</label>
              </div>

              <button type="submit" class="btn btn-primary">Update Password</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include "include/footer.php"; ?>

<script>
function togglePasswords() {
  const newPass = document.getElementById('new_password');
  const confirmPass = document.getElementById('confirm_password');
  const type = newPass.type === 'password' ? 'text' : 'password';
  newPass.type = type;
  confirmPass.type = type;
}
</script>

</body>
</html>
