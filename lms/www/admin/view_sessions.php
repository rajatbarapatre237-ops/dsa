<?php
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());
$result = $fun->getAllAcademicSessions();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>View Sessions</title>
  <?php include "include/links.php"; ?>
</head>
<body>
<?php include "include/header.php"; ?>
<?php include "include/sideBar.php"; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>View Sessions</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item">Sessions</li>
        <li class="breadcrumb-item active">View Sessions</li>
      </ol>
    </nav>
  </div>

  <?php if (isset($_GET['msg'])) {
      $msgText = (string) $_GET['msg'];
      $isError = stripos($msgText, 'cannot delete') !== false
          || stripos($msgText, 'not deleted') !== false
          || stripos($msgText, 'could not') !== false;
      ?>
  <p class="text-center <?php echo $isError ? 'text-danger' : 'text-success'; ?>">
    <?php echo htmlspecialchars($msgText); ?>
  </p>
  <?php } ?>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Academic sessions</h5>
              <a href="add_session.php" class="btn btn-primary btn-sm">Add session</a>
            </div>
            <div class="table-responsive">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Session</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    $i = 1;
                    while ($res = mysqli_fetch_assoc($result)) {
                        ?>
                  <tr>
                    <th scope="row"><?php echo $i; ?></th>
                    <td><?php echo htmlspecialchars($res['session_name']); ?></td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                          <?php echo ($res['status']) ? 'checked' : ''; ?>
                          onclick="toggleSession(<?php echo (int) $res['id']; ?>, <?php echo (int) $res['status']; ?>)">
                      </div>
                    </td>
                    <td class="text-nowrap">
                      <a href="edit_session.php?id=<?php echo (int) $res['id']; ?>"
                         class="btn btn-success btn-sm">Edit</a>
                      <a href="delete.php?session=1&amp;id=<?php echo (int) $res['id']; ?>"
                         class="btn btn-danger btn-sm"
                         onclick="return confirm('Delete this session? Students must be moved to another session first.');">Delete</a>
                    </td>
                  </tr>
                        <?php
                        ++$i;
                    }
                } else {
                    ?>
                  <tr>
                    <td colspan="4" class="text-muted">No sessions yet. <a href="add_session.php">Add one</a>.</td>
                  </tr>
                    <?php
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
function toggleSession(id, status) {
  fetch('verify.php?session&id=' + id + '&verify=' + status)
    .then(function () { window.location.reload(); });
}
</script>

<?php include "include/footer.php"; ?>
</body>
</html>
