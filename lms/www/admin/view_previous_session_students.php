<?php
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$filter = trim((string) ($_GET['session'] ?? ''));
$result = $fun->getArchivedSessionStudents($filter);
$sessionNames = $fun->getArchivedSessionNames();
$totalCount = $fun->countArchivedStudents($filter);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Previous Session Students</title>
  <?php include "include/links.php"; ?>
</head>
<body>
<?php include "include/header.php"; ?>
<?php include "include/sideBar.php"; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Previous Session Students</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item">Sessions</li>
        <li class="breadcrumb-item active">Archived Students</li>
      </ol>
    </nav>
  </div>

  <p class="text-center text-success"><?php
    if (isset($_GET['msg'])) {
        echo htmlspecialchars($_GET['msg']);
    }
  ?></p>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
          <h5 class="card-title mb-0">Archived records (<?php echo (int) $totalCount; ?>)</h5>
          <div class="d-flex gap-2">
            <a href="transfer_session_students.php" class="btn btn-primary btn-sm">Transfer more students</a>
          </div>
        </div>

        <form method="GET" class="row g-2 mb-3">
          <div class="col-md-4">
            <select name="session" class="form-select" onchange="this.form.submit()">
              <option value="">All archived sessions</option>
              <?php
              if ($sessionNames) {
                  while ($sn = mysqli_fetch_assoc($sessionNames)) {
                      $name = $sn['session_name'];
                      ?>
                <option value="<?php echo htmlspecialchars($name); ?>"
                  <?php echo ($filter === $name) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($name); ?>
                </option>
                      <?php
                  }
              }
              ?>
            </select>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-striped datatable">
            <thead>
              <tr>
                <th>#</th>
                <th>Original ID</th>
                <th>Name</th>
                <th>Session</th>
                <th>Course</th>
                <th>Batch</th>
                <th>Mobile</th>
                <th>Transferred</th>
                <th>By</th>
              </tr>
            </thead>
            <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $i = 1;
                while ($res = mysqli_fetch_assoc($result)) {
                    ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo (int) $res['original_student_id']; ?></td>
                <td><?php echo htmlspecialchars($res['name']); ?></td>
                <td><?php echo htmlspecialchars($res['session_name']); ?></td>
                <td><?php echo htmlspecialchars($res['course_name']); ?></td>
                <td><?php echo htmlspecialchars($res['batch']); ?></td>
                <td><?php echo htmlspecialchars($res['mobile']); ?></td>
                <td><?php echo htmlspecialchars($res['transferred_at']); ?></td>
                <td><?php echo htmlspecialchars($res['transferred_by'] ?? '—'); ?></td>
              </tr>
                    <?php
                }
            } else {
                ?>
              <tr>
                <td colspan="9" class="text-muted text-center">
                  No archived students yet.
                  <a href="transfer_session_students.php">Transfer a session</a>
                </td>
              </tr>
                <?php
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include "include/footer.php"; ?>
</body>
</html>
