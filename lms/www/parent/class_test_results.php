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
        <li class="breadcrumb-item"><a href="parent_dashboard.php">Home</a></li>
        <li class="breadcrumb-item active">Results</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card mb-3">
      <div class="card-body row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label">Course</label>
          <select id="course_id" class="form-select"><option value="">Loading…</option></select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Subject</label>
          <select id="subject_id" class="form-select" disabled><option value="">Select subject</option></select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Test</label>
          <select id="test_id" class="form-select" disabled><option value="">Select test</option></select>
        </div>
        <div class="col-12">
          <button type="button" id="btnLoad" class="btn btn-primary">Load result</button>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div id="metaBox" class="mb-3"></div>
        <div class="table-responsive">
          <table class="table table-bordered" id="resultTable" style="display:none;">
            <thead>
              <tr>
                <th>Student</th>
                <th>Marks</th>
                <th>Total</th>
                <th>Passing</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="resultBody"></tbody>
          </table>
        </div>
        <p id="emptyMsg" class="text-muted">Your child’s course is pre-selected when available.</p>
      </div>
    </div>
  </section>
</main>

<?php include 'include/footer.php'; ?>

<script>
(function () {
  const CT_API = '../class_test_api.php';
  const courseEl = document.getElementById('course_id');
  const subjectEl = document.getElementById('subject_id');
  const testEl = document.getElementById('test_id');

  fetch(CT_API + '?action=courses').then(r => r.json()).then(data => {
    courseEl.innerHTML = '<option value="">Select course</option>';
    if (!data.ok) return;
    data.courses.forEach(c => {
      const o = document.createElement('option');
      o.value = c.id;
      o.textContent = c.course_name;
      courseEl.appendChild(o);
    });
    if (data.locked_course_id) {
      courseEl.value = String(data.locked_course_id);
      courseEl.dispatchEvent(new Event('change'));
      courseEl.disabled = true;
    }
  });

  courseEl.addEventListener('change', () => {
    subjectEl.innerHTML = '<option value="">Select subject</option>';
    subjectEl.disabled = true;
    testEl.innerHTML = '<option value="">Select test</option>';
    testEl.disabled = true;
    const cid = courseEl.value;
    if (!cid) return;
    fetch(CT_API + '?action=subjects&course_id=' + cid).then(r => r.json()).then(data => {
      if (!data.ok) return;
      data.subjects.forEach(s => {
        const o = document.createElement('option');
        o.value = s.id;
        o.textContent = s.subject_name;
        subjectEl.appendChild(o);
      });
      subjectEl.disabled = false;
    });
  });

  subjectEl.addEventListener('change', () => {
    testEl.innerHTML = '<option value="">Select test</option>';
    testEl.disabled = true;
    const cid = courseEl.value;
    const sid = subjectEl.value;
    if (!cid || !sid) return;
    fetch(CT_API + '?action=tests&course_id=' + cid + '&subject_id=' + sid).then(r => r.json()).then(data => {
      if (!data.ok) return;
      data.tests.forEach(t => {
        const o = document.createElement('option');
        o.value = t.id;
        o.textContent = t.test_name + ' (' + t.test_date + ')';
        testEl.appendChild(o);
      });
      testEl.disabled = false;
    });
  });

  document.getElementById('btnLoad').addEventListener('click', () => {
    const cid = courseEl.value, sid = subjectEl.value, tid = testEl.value;
    if (!cid || !sid || !tid) { alert('Select course, subject, and test.'); return; }
    fetch(CT_API + '?action=results&course_id=' + cid + '&subject_id=' + sid + '&test_id=' + tid)
      .then(r => r.json())
      .then(data => {
        const tbl = document.getElementById('resultTable');
        const body = document.getElementById('resultBody');
        const metaBox = document.getElementById('metaBox');
        const emptyMsg = document.getElementById('emptyMsg');
        body.innerHTML = '';
        if (!data.ok) {
          emptyMsg.textContent = data.error || 'Error';
          tbl.style.display = 'none';
          return;
        }
        const m = data.meta;
        metaBox.innerHTML = '<strong>' + m.test_name + '</strong> — ' + m.course_name + ' / ' + m.subject_name;
        if (!data.rows.length) {
          tbl.style.display = 'none';
          emptyMsg.textContent = 'No marks recorded yet.';
          return;
        }
        data.rows.forEach(row => {
          const tr = document.createElement('tr');
          const marks = row.marks_obtained != null ? row.marks_obtained : '—';
          const status = row.status || '—';
          tr.innerHTML = '<td>' + row.student_name + '</td><td>' + marks + '</td><td>' + row.total_marks + '</td><td>' +
            row.passing_marks + '</td><td>' + status + '</td>';
          body.appendChild(tr);
        });
        tbl.style.display = 'table';
        emptyMsg.style.display = 'none';
      });
  });
})();
</script>
</body>
</html>
