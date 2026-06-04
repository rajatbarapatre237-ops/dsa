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
							<div class="title"><h4>All Test Marks</h4></div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
									<li class="breadcrumb-item active">All test marks</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div class="card-box mb-30">
					<div class="pd-20">
						<div id="metaBox" class="mb-3"></div>
						<div class="table-responsive pb-20">
							<table class="table table-bordered" id="marksTable" style="display:none;">
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
						<p id="emptyMsg" class="text-muted">Loading your test marks…</p>
					</div>
				</div>
			</div>
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
      emptyMsg.textContent = 'No class tests found for your course yet.';
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
