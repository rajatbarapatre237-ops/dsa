<?php
    include "connect/db.php";
    include "connect/fun.php";
    include 'include/auth_session.php';

    $connect = new connect();
    $fun = new fun($connect->dbconnect());
     if(isset($_GET['imagegallery'])){
        $id = $_GET['img_id'];
        $delete = $fun->deletegallery($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_gallery.php?msg=$msg");
exit; // Ensure the script stops execution after redirect


    }
    ?>