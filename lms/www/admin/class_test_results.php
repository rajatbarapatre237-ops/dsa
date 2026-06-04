<?php
include 'connect/db.php';
include 'include/auth_session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Class Test Results</title>
  <?php include 'include/links.php'; ?>
</head>
<body>
<?php include 'include/header.php'; ?>
<?php include 'include/sideBar.php'; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Class Test Results</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item">Assignments</li>
        <li class="breadcrumb-item active">Results</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card mb-3">
      <div class="card-body row g-3 align-items-end">
        <div class="col-md-4 col-lg-2">
          <label class="form-label">Session</label>
          <select id="session_id" class="form-select"><option value="">Select session</option></select>
        </div>
        <div class="col-md-4 col-lg-2">
          <label class="form-label">Course</label>
          <select id="course_id" class="form-select" disabled><option value="">Select course</option></select>
        </div>
        <div class="col-md-4 col-lg-2">
          <label class="form-label">Subject</label>
          <select id="subject_id" class="form-select" disabled><option value="">Select subject</option></select>
        </div>
        <div class="col-md-4 col-lg-2">
          <label class="form-label">Test</label>
          <select id="test_id" class="form-select" disabled><option value="">Select test</option></select>
        </div>
        <div class="col-md-4 col-lg-2">
          <label class="form-label">Batch <small class="text-muted">(optional)</small></label>
          <select id="batch" class="form-select" disabled><option value="">All batches</option></select>
        </div>
        <div class="col-md-4 col-lg-2">
          <button type="button" id="btnLoad" class="btn btn-primary w-100">Load results</button>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div id="metaBox" class="mb-3"></div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="resultTable" style="display:none;">
            <thead>
              <tr>
                <th>#</th>
                <th>Student</th>
                <th>Student Id</th>
                <th>Marks</th>
                <th>Total</th>
                <th>Passing</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="resultBody"></tbody>
          </table>
        </div>
        <p id="emptyMsg" class="text-muted">Select session, course, subject, and test, then load results.</p>
      </div>
    </div>
  </section>
</main>

<?php include 'include/footer.php'; ?>

<script src="../assets/js/class_test_filters.js"></script>
<script>
ClassTestFilters.init({ api: '../class_test_api.php', mode: 'results', credentials: 'same-origin' });
</script>
</body>
</html>
