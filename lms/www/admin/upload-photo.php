
<?php
include "../gallery/dbc.php";
include 'include/auth_session.php';
if (isset($_POST['btn_upload']))
  {
    $filetmp   = $_FILES["file_img"]["tmp_name"];
    $filename  = $_FILES["file_img"]["name"];
    $filetype  = $_FILES["file_img"]["type"];
    $filepath  = "../gallery/images/" . $filename;
    $filetitle = $_POST['img_title'];
    
    move_uploaded_file($filetmp, $filepath);
    
    $stmt = $dbc->prepare("INSERT INTO tbl_photos (img_name, img_type, img_path, img_title) VALUES (:iname, :itype, :ipath, :ititle) ");
    $stmt->bindValue(':iname', $filename);
    $stmt->bindValue('itype', $filetype);
    $stmt->bindValue('ipath', $filepath);
    $stmt->bindValue('ititle', $filetitle);
    if ($stmt->execute())
      {
        header('Location: upload-photo.php?success=yes&title=' . $filetitle);
      }
    else
      {
        header('Location: upload-photo.php?success=no');
      }
  }
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Gallery Upload</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php 
    include "include/links.php";
  ?>
</head>

<body>

  <!-- ======= Header ======= -->
  <?php 
    include "include/header.php";
  ?>
  <!-- End Header -->
      <?php 
        include "include/sideBar.php";
      ?>
  <!-- ======= Sidebar ======= -->
  
  <!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
        <h1>Gallery Upload</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Front Page Customisation</li>
            <li class="breadcrumb-item active">Gallery Upload</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="card">
            <div class="card-body">
            <?php
            if(isset($_GET['success'])) {
            if($_GET['success']=="yes"){?>
            <div class="row">
               <div class="small-6 large-6 columns">
                  <div data-alert class="alert-box success radius ">
                     Image "<?= $_GET['title']; ?>" uploaded successfully.
                     <a href="#" class="close">&times;</a>
                  </div>
               </div>
            </div>
            <?php } else {?>
             <div class="row">
               <div class="small-6 large-6 columns">
                  <div data-alert class="alert-box alert radius ">
                     There was a problem uploading the image.
                     <a href="#" class="close">&times;</a>
                  </div>
               </div>
            </div>
            <?php } }?>
              <h5 class="card-title"></h5>

              <!-- No Labels Form -->
              <form class="row g-3"action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" data-abide>
                
                <div class="col-md-12">
                  <label>Upload Photo To Gallery</label>
                  <input type="file" class="form-control" name="file_img" pattern="^.+?\.(jpg|JPG|png|PNG)$" required>
                </div>
                <div class="col-md-12">
                <label>Enter Title</label>
                  <input type="text" class="form-control" name="img_title" placeholder="Image title" required>
                </div>
               
                
                
                    
                 
                             
                <div class="text-center">
                <input type="submit" value="Upload Image" name="btn_upload" class="button">
                  
                </div>
                
              </form><!-- End No Labels Form -->

            </div>
          </div>
      </section>

    <script>
        document.getElementById('date').valueAsDate = new Date();
    </script>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
<?php 
  include "include/footer.php";
?>

</body>
<a class="close-reveal-modal">&#215;</a>
</html>