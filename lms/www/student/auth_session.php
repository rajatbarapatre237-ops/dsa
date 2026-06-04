<?php
    session_start();
    if(!isset($_SESSION["sid"])&& !$_SESSION['is_start']) {
        header("Location: index.php");
        exit();
    }
    
?>