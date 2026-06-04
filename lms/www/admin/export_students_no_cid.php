<?php
/**
 * Standalone utility (no login). Export active students whose CID is not set.
 * Open from anywhere: /admin/export_students_no_cid.php
 * CSV: /admin/export_students_no_cid.php?download=1
 * Selected: POST ids[] with download_selected=1
 */
include "connect/db.php";
include "connect/fun.php";

$connect = new connect();
$fun = new fun($connect->dbconnect());
$db = $connect->dbconnect();

function export_no_cid_csv($result, mysqli $db): void
{
    $filename = 'students_no_cid_' . date('Y-m-d_His') . '.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    fprintf($out, "\xEF\xBB\xBF");

    if ($result && mysqli_num_rows($result) > 0) {
        $first = mysqli_fetch_assoc($result);
        $headers = array_keys($first);
        fputcsv($out, $headers);
        fputcsv($out, array_values($first));
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($out, array_values($row));
        }
    } else {
        $cols = [];
        $colRes = mysqli_query($db, 'SHOW COLUMNS FROM `stud_details`');
        if ($colRes) {
            while ($c = mysqli_fetch_assoc($colRes)) {
                $cols[] = $c['Field'];
            }
        }
        if ($cols) {
            fputcsv($out, $cols);
        }
    }

    fclose($out);
}

if (isset($_GET['download'])) {
    $result = $fun->fetchStudentsWithoutCidForExport();
    export_no_cid_csv($result, $db);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_selected'])) {
    $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? $_POST['ids'] : [];
    $result = $fun->fetchStudentsWithoutCidByIds($ids);
    export_no_cid_csv($result, $db);
    exit;
}

$count = $fun->countStudentsWithoutCid();
$preview = $fun->fetchStudentsWithoutCidForExport();
$rows = [];
if ($preview) {
    while ($row = mysqli_fetch_assoc($preview)) {
        $rows[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Export Students (No CID)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { padding: 1.5rem; background: #f6f9ff; }
    .wrap { max-width: 1200px; margin: 0 auto; }
    .table thead th { white-space: nowrap; }
    #selectAll { width: 1.1rem; height: 1.1rem; cursor: pointer; }
    .row-check { width: 1.1rem; height: 1.1rem; cursor: pointer; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h4 mb-2">Students without CID</h1>
        <p class="text-muted small mb-3">
          Lists active students in <code>stud_details</code> where CID is empty (photo/card not marked complete).
          No admin login required — bookmark or open this link from any device.
        </p>

        <p class="mb-3">
          <strong><?php echo (int) $count; ?></strong> student(s) match.
          <?php if (count($rows) > 0) { ?>
            <span class="text-muted">Showing all <?php echo count($rows); ?> in the table below.</span>
          <?php } ?>
        </p>

        <div class="d-flex flex-wrap gap-2 mb-4">
          <a href="export_students_no_cid.php?download=1" class="btn btn-success">
            Download CSV (all)
          </a>
          <button type="submit" form="exportForm" name="download_selected" value="1" class="btn btn-outline-success">
            Download CSV (selected)
          </button>
          <a href="click_img.php" class="btn btn-outline-primary">Open click image page</a>
        </div>

        <?php if (count($rows) > 0) { ?>
          <form id="exportForm" method="post" action="export_students_no_cid.php">
            <h2 class="h6">All students (no CID)</h2>
            <div class="table-responsive">
              <table class="table table-sm table-bordered bg-white">
                <thead>
                  <tr>
                    <th style="width:42px;">
                      <input type="checkbox" id="selectAll" title="Select all" checked>
                    </th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Batch</th>
                    <th>Session</th>
                    <th>Mobile</th>
                    <th>CID</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row) { ?>
                  <tr>
                    <td>
                      <input type="checkbox" class="row-check" name="ids[]"
                        value="<?php echo (int) $row['id']; ?>" checked>
                    </td>
                    <td><?php echo (int) $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['course_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['batch'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['session_name'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($row['mobile'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['CID'] ?? ''); ?></td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </form>
        <?php } else { ?>
          <p class="text-muted mb-0">No students with empty CID right now.</p>
        <?php } ?>
      </div>
    </div>
  </div>
  <script>
  (function () {
    var selectAll = document.getElementById('selectAll');
    if (!selectAll) return;
    var rowChecks = document.querySelectorAll('.row-check');

    selectAll.addEventListener('change', function () {
      rowChecks.forEach(function (cb) {
        cb.checked = selectAll.checked;
      });
    });

    rowChecks.forEach(function (cb) {
      cb.addEventListener('change', function () {
        var all = true;
        var any = false;
        rowChecks.forEach(function (c) {
          if (c.checked) any = true;
          else all = false;
        });
        selectAll.checked = all;
        selectAll.indeterminate = any && !all;
      });
    });
  })();
  </script>
</body>
</html>
