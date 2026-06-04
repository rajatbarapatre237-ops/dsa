<?php
include 'connect/db.php';
include 'auth_session.php';
?>
<!DOCTYPE html>
<html>
<?php include 'include/head.php'; ?>
<body>
	<?php include 'include/sidebar.php'; ?>
	<div class="main-container" style="margin-top: -50px">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title"><h4>Class Test Results</h4></div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
									<li class="breadcrumb-item active">Class test results</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div class="card-box mb-30">
					<div class="pd-20">
						<div class="row g-3 mb-3">
							<div class="col-md-4">
								<label class="form-label">Course</label>
								<select id="course_id" class="form-control"><option value="">Loading…</option></select>
							</div>
							<div class="col-md-4">
								<label class="form-label">Subject</label>
								<select id="subject_id" class="form-control" disabled><option value="">Select subject</option></select>
							</div>
							<div class="col-md-4">
								<label class="form-label">Test</label>
								<select id="test_id" class="form-control" disabled><option value="">Select test</option></select>
							</div>
							<div class="col-12">
								<button type="button" id="btnLoad" class="btn btn-primary">Load my result</button>
							</div>
						</div>

						<div id="metaBox" class="mb-3"></div>
						<div class="table-responsive pb-20">
							<table class="table table-bordered" id="resultTable" style="display:none;">
								<thead>
									<tr>
										<th>Test</th>
										<th>Marks</th>
										<th>Total</th>
										<th>Passing</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody id="resultBody"></tbody>
							</table>
						</div>
						<p id="emptyMsg" class="text-muted">Your enrolled course is pre-selected where applicable.</p>
					</div>
				</div>
			</div>
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
        metaBox.innerHTML = '<strong>' + m.test_name + '</strong> — ' + m.course_name + ' / ' + m.subject_name +
          '<br><small>' + m.test_date + '</small>';
        if (!data.rows.length) {
          tbl.style.display = 'none';
          emptyMsg.textContent = 'No result row for your account yet.';
          return;
        }
        data.rows.forEach(row => {
          const tr = document.createElement('tr');
          const marks = row.marks_obtained != null ? row.marks_obtained : '—';
          const status = row.status || '—';
          tr.innerHTML = '<td>' + m.test_name + '</td><td>' + marks + '</td><td>' + row.total_marks + '</td><td>' +
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
