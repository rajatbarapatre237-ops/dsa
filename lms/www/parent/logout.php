<?php
session_start();
setcookie("remember_parent", "", time() - 3600, "/");
session_destroy();
header("Location: index.php");
exit();
?>