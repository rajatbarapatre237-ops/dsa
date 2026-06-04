<?php
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$message = '';
$messageType = 'danger';
$transferOptions = $fun->getSessionTransferOptions();

if (isset($_POST['transfer'])) {
    $sessionKey = $_POST['session_key'] ?? '';
    $confirm = $_POST['confirm_text'] ?? '';
    $adminUser = (string) ($_SESSION['username'] ?? 'admin');

    if ($sessionKey === '') {
        $message = 'Please select a session to transfer.';
    } elseif (strtoupper(trim($confirm)) !== 'TRANSFER') {
        $message = 'Type TRANSFER in the confirmation box to proceed.';
    } else {
        $result = $fun->transferSessionStudentsToArchive($sessionKey, $adminUser);
        if ($result['ok']) {
            header('Location: view_previous_session_students.php?msg=' . urlencode(
                $result['count'] . ' student(s) moved to previous session records.'
            ));
            exit;
        }
        $message = $result['error'] ?? 'Transfer failed.';
    }
}

$selectedKey = isset($_POST['transfer']) ? ($_POST['session_key'] ?? '') : ($_GET['session'] ?? '');
$previewCount = $selectedKey !== '' ? $fun->countActiveStudentsForSessionKey($selectedKey) : 0;
$totalArchived = $fun->countArchivedStudents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Transfer Session Students</title>
  <?php include "include/links.php"; ?>
</head>
<body>
<?php include "include/header.php"; ?>
<?php include "include/sideBar.php"; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Transfer Previous Session Students</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item">Sessions</li>
        <li class="breadcrumb-item active">Transfer Students</li>
      </ol>
    </nav>
  </div>

  <?php if ($message !== '') { ?>
    <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
  <?php } ?>

  <section class="section">
    <div class="row">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Move students to archive</h5>
            <p class="text-muted small">
              Select an academic session. All active students in that session are copied to
              <strong>stud_details_previous_session</strong>, then removed from the active student list
              (including login accounts). Attendance and test marks history are kept in the database.
            </p>

            <?php if (count($transferOptions) === 0) { ?>
              <div class="alert alert-info mb-0">
                No active students are available to transfer. Students may already be archived or none are enrolled.
              </div>
            <?php } else { ?>
              <form method="POST" action="transfer_session_students.php" id="transferForm">
                <div class="mb-3">
                  <label class="form-label">Session to archive</label>
                  <select name="session_key" id="session_key" class="form-select" required>
                    <option value="">Select session</option>
                    <?php foreach ($transferOptions as $opt) { ?>
                      <option value="<?php echo htmlspecialchars($opt['key']); ?>"
                        <?php echo ($selectedKey === $opt['key']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($opt['label']); ?>
                        (<?php echo (int) $opt['count']; ?> student<?php echo $opt['count'] !== 1 ? 's' : ''; ?>)
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <?php if ($selectedKey !== '') { ?>
                  <div class="alert alert-warning">
                    <strong><?php echo (int) $previewCount; ?></strong> student(s) will be moved to the previous-session table
                    and removed from <strong>View Student</strong> / marks / attendance lists.
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Type <strong>TRANSFER</strong> to confirm</label>
                    <input type="text" name="confirm_text" class="form-control" placeholder="TRANSFER" autocomplete="off" required>
                  </div>
                  <button type="submit" name="transfer" class="btn btn-danger"
                    onclick="return confirm('This cannot be undone easily. Move all students for this session to the archive?');">
                    Transfer to previous session table
                  </button>
                <?php } ?>
              </form>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Archive summary</h5>
            <p class="mb-2">Students already archived: <strong><?php echo (int) $totalArchived; ?></strong></p>
            <a href="view_previous_session_students.php" class="btn btn-outline-primary btn-sm">
              View previous session students
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
(function () {
  var sel = document.getElementById('session_key');
  if (sel) {
    sel.addEventListener('change', function () {
      if (sel.value) {
        window.location = 'transfer_session_students.php?session=' + encodeURIComponent(sel.value);
      }
    });
  }
})();
</script>

<?php include "include/footer.php"; ?>
</body>
</html>
