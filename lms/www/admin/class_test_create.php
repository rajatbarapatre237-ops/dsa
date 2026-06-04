<?php
include 'connect/db.php';
include 'include/auth_session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Class Test</title>
  <?php include 'include/links.php'; ?>
</head>
<body>
<?php include 'include/header.php'; ?>
<?php include 'include/sideBar.php'; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>Create Class Test</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
        <li class="breadcrumb-item">Assignments</li>
        <li class="breadcrumb-item active">Class Test</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">New class test</h5>
        <div id="alertBox"></div>
        <form id="ctForm" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Course</label>
            <select id="course_id" class="form-select" required>
              <option value="">Select course</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Subject</label>
            <select id="subject_id" class="form-select" required disabled>
              <option value="">Select subject</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Test name</label>
            <input type="text" id="test_name" class="form-control" required maxlength="255">
          </div>
          <div class="col-md-6">
            <label class="form-label">Test date</label>
            <input type="date" id="test_date" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Total marks</label>
            <input type="number" step="0.01" min="1" id="total_marks" class="form-control" value="100">
          </div>
          <div class="col-md-6">
            <label class="form-label">Passing marks</label>
            <input type="number" step="0.01" min="0" id="passing_marks" class="form-control" value="40">
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Create test</button>
            <a href="class_test_results.php" class="btn btn-outline-secondary">View results</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<?php include 'include/footer.php'; ?>

<script>
(function () {
  const CT_API = '../class_test_api.php';
  const alertBox = document.getElementById('alertBox');
  const courseEl = document.getElementById('course_id');
  const subjectEl = document.getElementById('subject_id');

  function showAlert(msg, ok) {
    alertBox.innerHTML = '<div class="alert alert-' + (ok ? 'success' : 'danger') + '">' + msg + '</div>';
  }

  fetch(CT_API + '?action=courses').then(r => r.json()).then(data => {
    if (!data.ok) { showAlert(data.error || 'Could not load courses', false); return; }
    data.courses.forEach(c => {
      const o = document.createElement('option');
      o.value = c.id;
      o.textContent = c.course_name;
      courseEl.appendChild(o);
    });
  }).catch(() => showAlert('Network error loading courses', false));

  courseEl.addEventListener('change', () => {
    subjectEl.innerHTML = '<option value="">Select subject</option>';
    subjectEl.disabled = true;
    const cid = courseEl.value;
    if (!cid) return;
    fetch(CT_API + '?action=subjects&course_id=' + encodeURIComponent(cid))
      .then(r => r.json())
      .then(data => {
        if (!data.ok) { showAlert(data.error || 'Subjects failed', false); return; }
        data.subjects.forEach(s => {
          const o = document.createElement('option');
          o.value = s.id;
          o.textContent = s.subject_name;
          subjectEl.appendChild(o);
        });
        subjectEl.disabled = false;
      });
  });

  document.getElementById('ctForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('action', 'create_test');
    fd.append('course_id', courseEl.value);
    fd.append('subject_id', subjectEl.value);
    fd.append('test_name', document.getElementById('test_name').value.trim());
    fd.append('test_date', document.getElementById('test_date').value);
    fd.append('total_marks', document.getElementById('total_marks').value);
    fd.append('passing_marks', document.getElementById('passing_marks').value);

    fetch(CT_API, { method: 'POST', body: fd })
      .then(r => r.json())
      .then(data => {
        if (data.ok) {
          showAlert('Class test created.', true);
          document.getElementById('test_name').value = '';
        } else {
          showAlert(data.error || 'Could not create test', false);
        }
      })
      .catch(() => showAlert('Network error', false));
  });
})();
</script>
</body>
</html>
