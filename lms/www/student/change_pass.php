<?php
session_start();
include "connect/db.php";

// Check if parent is logged in
if (!isset($_SESSION['sid'])) {
    header("Location: index.php");
    exit();
}

// Connect to DB using your connect class
$connect = new connect();
$conn = $connect->dbconnect();

$student_id = preg_replace('/\D/', '', $_SESSION['sid']);

$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Fetch current password from DB
    $stmt = $conn->prepare("SELECT pass FROM users WHERE sid = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($db_pass);
    $stmt->fetch();
    $stmt->close();

    if ($current === $db_pass) {
        if ($new === $confirm) {
            $update = $conn->prepare("UPDATE users SET pass = ? WHERE sid = ?");
            $update->bind_param("si", $new, $student_id);
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
                                <h4>Change Password</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index3.html">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="card-box p-4">
                    <h5 class="mb-4">Update Your Password</h5>

                    <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

                    <form method="post">
              <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="text" name="old_password" class="form-control" required>
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