<?php
session_start();
include 'connect/db.php';
if (!isset($_SESSION['parent_id'])) {
  header('Location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>All Test Marks</title>
  <?php include 'include/links.php'; ?>
</head>
<body>
<?php include 'include/header.php'; ?>
<?php include 'include/sideBar.php'; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>All Test Marks</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="parent_dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">All test marks</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <div id="metaBox" class="mb-3"></div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="marksTable" style="display:none;">
            <thead>
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Test name</th>
                <th>Subject</th>
                <th>Obtained</th>
                <th>Total</th>
                <th>Passing</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="marksBody"></tbody>
          </table>
        </div>
        <p id="emptyMsg" class="text-muted">Loading test marks…</p>
      </div>
    </div>
  </section>
</main>

<?php include 'include/footer.php'; ?>

<script>
(function () {
  var API = '../class_test_api.php';

  function parseJson(r) {
    return r.text().then(function (txt) {
      var data = null;
      try { data = txt ? JSON.parse(txt) : null; } catch (e) { data = null; }
      if (!r.ok) throw new Error((data && data.error) ? data.error : ('HTTP ' + r.status));
      if (!data) throw new Error('Invalid API response');
      return data;
    });
  }

  function renderMarks(data) {
    var tbl = document.getElementById('marksTable');
    var body = document.getElementById('marksBody');
    var metaBox = document.getElementById('metaBox');
    var emptyMsg = document.getElementById('emptyMsg');
    body.innerHTML = '';
    if (!data.ok) {
      emptyMsg.textContent = data.error || 'Error';
      emptyMsg.style.display = 'block';
      tbl.style.display = 'none';
      metaBox.innerHTML = '';
      return;
    }
    var st = data.student;
    var uid = st.uid ? st.uid : ('DSA' + st.id);
    metaBox.innerHTML = '<strong>' + st.name + '</strong> — ' + uid +
      '<br><small class="text-muted">' + st.course_name +
      (st.batch ? ' | Batch: ' + st.batch : '') +
      (st.session_name ? ' | Session: ' + st.session_name : '') + '</small>';

    if (!data.rows.length) {
      tbl.style.display = 'none';
      emptyMsg.textContent = 'No class tests found for your child\'s course yet.';
      emptyMsg.style.display = 'block';
      return;
    }
    data.rows.forEach(function (row, i) {
      var tr = document.createElement('tr');
      var marks = row.marks_obtained != null ? row.marks_obtained : '—';
      var status = row.status || '—';
      var statusClass = row.status === 'Pass' ? 'text-success' : (row.status === 'Fail' ? 'text-danger' : '');
      tr.innerHTML = '<td>' + (i + 1) + '</td><td>' + row.test_date + '</td><td>' + row.test_name + '</td><td>' +
        row.subject_name + '</td><td>' + marks + '</td><td>' + row.total_marks + '</td><td>' +
        row.passing_marks + '</td><td class="' + statusClass + '">' + status + '</td>';
      body.appendChild(tr);
    });
    tbl.style.display = 'table';
    emptyMsg.style.display = 'none';
  }

  fetch(API + '?action=student_all_marks', { credentials: 'same-origin' })
    .then(parseJson)
    .then(renderMarks)
    .catch(function (err) {
      document.getElementById('emptyMsg').textContent = err.message || 'Could not load marks.';
    });
})();
</script>
</body>
</html>
