<?php 
include "connect/db.php";
include "connect/fun.php";
// include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$fetch = $fun->fetchAllStudentscid();

/**
 * Resize (max edge) and save as JPEG to reduce file size.
 */
function click_img_save_compressed(string $tmpPath, string $destJpegPath, int $maxEdge = 1280, int $jpegQuality = 82): bool
{
    if (!function_exists('imagecreatefromstring') || !function_exists('imagejpeg')) {
        return false;
    }
    $bin = @file_get_contents($tmpPath);
    if ($bin === false || $bin === '') {
        return false;
    }
    $src = @imagecreatefromstring($bin);
    if ($src === false) {
        return false;
    }
    $w = imagesx($src);
    $h = imagesy($src);
    if ($w < 1 || $h < 1) {
        imagedestroy($src);
        return false;
    }

    if ($w > $maxEdge || $h > $maxEdge) {
        $ratio = min($maxEdge / $w, $maxEdge / $h);
        $nw = max(1, (int) round($w * $ratio));
        $nh = max(1, (int) round($h * $ratio));
        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, false);
        $bg = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $nw, $nh, $bg);
        imagealphablending($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);
        $src = $dst;
    }

    $ok = imagejpeg($src, $destJpegPath, $jpegQuality);
    imagedestroy($src);
    return $ok;
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['img']) && isset($_POST['id'])) {
    $studentId = $_POST['id'];
    $uploadDir = 'student_pfp/';

    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Always store compressed JPEG for smaller size
    $newFileName = $uploadDir . $studentId . '.jpg';
    $newFile = $studentId . '.jpg';

    $tmp = $_FILES['img']['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        $msg = "❌ Invalid upload.";
    } elseif (click_img_save_compressed($tmp, $newFileName)) {
        foreach (['jpeg', 'png', 'gif', 'webp'] as $oldExt) {
            $oldPath = $uploadDir . $studentId . '.' . $oldExt;
            if (is_file($oldPath) && $oldPath !== $newFileName) {
                @unlink($oldPath);
            }
        }
        $cid = $fun->updatecid($studentId, $newFile);
        $msg = "✅ Image uploaded (compressed) as <b>$studentId.jpg</b>";
    } else {
        $ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $ext = 'jpg';
        }
        $fallbackPath = $uploadDir . $studentId . '.' . $ext;
        $fallbackFile = $studentId . '.' . $ext;
        if (@move_uploaded_file($tmp, $fallbackPath)) {
            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $e) {
                $p = $uploadDir . $studentId . '.' . $e;
                if ($p !== $fallbackPath && is_file($p)) {
                    @unlink($p);
                }
            }
            $cid = $fun->updatecid($studentId, $fallbackFile);
            $msg = "✅ Image uploaded as <b>$fallbackFile</b> (compression unavailable — enable GD extension).";
        } else {
            $msg = "❌ Image upload failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>View Student</title>
  <?php include "include/links.php"; ?>
  <style>
    form.student-upload-form {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0;
    }
    form.student-upload-form > * {
      flex: 1 1 auto;
    }
    form.student-upload-form input[type="submit"] {
      flex: 0 0 auto;
      width: auto;
    }
  </style>
</head>

<body>
  <?php include "include/header.php"; ?>
  <?php include "include/sideBar.php"; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Click Images</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Students</li>
          <li class="breadcrumb-item active">Click Image</li>
        </ol>
      </nav>
    </div>

    <p class="text-center text-success"><?= $msg ?>
    </p>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Student Table</h5>
              <div class="table-responsive">
                <table class="table datatable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Student ID</th>
                      <th>Name</th>
                      <th>Upload Image</th>
                      <th>Submit</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sr = 1;
                      while ($res = mysqli_fetch_assoc($fetch)) {
                    ?>
                    <tr>
                      <td colspan="5" style="padding: 0;">
                        <form method="post" action="" enctype="multipart/form-data" class="student-upload-form">
                          <span style="flex: 0 0 30px; padding-left: 10px;"><?= $sr ?></span>
                          
                          <span style="flex: 1 1 120px;"><?= "DSA" . $res['id'] ?></span>
                          <input type="hidden" name="id" value="<?= $res['id'] ?>">
                          
                          <span style="flex: 2 1 200px;"><?= htmlspecialchars($res['name']) ?></span>
                          
                          <input type="file" name="img" accept="image/*" capture="environment" required style="flex: 3 1 auto;">
                          
                          <input type="submit" name="submit" value="Submit" class="btn btn-primary btn-sm" style="flex: 0 0 auto; margin-right: 10px;">
                        </form>
                      </td>
                    </tr>
                    <?php $sr++; } ?>
                  </tbody>
                </table>
              </div> <!-- end table-responsive -->
            </div> <!-- end card-body -->
          </div> <!-- end card -->

        </div> <!-- end col-lg-12 -->
      </div> <!-- end row -->
    </section>

  </main>

  <?php include "include/footer.php"; ?>

</body>
</html>
