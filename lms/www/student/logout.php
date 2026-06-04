<?php
session_start();
unset($_SESSION['sid']);


unset($_SESSION['is_start']);

session_destroy();
header('location:index.php');


?>