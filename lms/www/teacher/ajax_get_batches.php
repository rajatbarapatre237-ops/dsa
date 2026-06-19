<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include "connect/db.php";
include "connect/fun.php";

$connect = new connect();
$fun = new fun($connect->dbconnect());

if(isset($_POST['course']))
{
    $course = trim($_POST['course']);

    echo "<option value=''>Select Batch</option>";

    $batches = $fun->getBatchByCourse($course);

    while($row = mysqli_fetch_assoc($batches))
    {
        echo "<option value='".$row['name']."'>".$row['name']."</option>";
    }

    exit;
}
?>