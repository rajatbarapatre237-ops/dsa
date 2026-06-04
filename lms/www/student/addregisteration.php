<?php 
    include "connect/db.php";
    include "connect/fun.php";

    $connect = new connect();
    $fun = new fun($connect->dbconnect());
    $conn = $connect->dbconnect();
    if(isset($_POST['submit'])){
        $username = $_POST['sid'];
        $id = substr($username, 3);
        $stud = $fun->getStudentByID($id);

        $sql  = "SELECT COUNT(`sid`) AS user FROM users  WHERE `sid` = '$id';";
        $fetch = mysqli_query($conn,$sql);
        $assoc = mysqli_fetch_assoc($fetch);
        if($assoc['user'] == 0 && mysqli_num_rows($stud) == 1){
            $agent = $fun->regStudent($_POST);
            if(!$agent){
                $agent = 'something Went Wrong'.$agent;
                header("Location: register.php?msg=$agent");
            }
            else{
                session_start();
                $_SESSION['sid'] = "ACE".$id;
                $_SESSION['is_start'] = true;
                header("Location: dashboard.php");
            }
        }
        else{
            if($assoc['user'] == 1){
                $agent = "Student already Registered ";

            }
            else if(mysqli_num_rows($stud) == 0){
                $agent = "Student Not exist";

            }
            else{
                $agent = "Student ";
            }
            header("Location: register.php?msg=$agent");
        }
        
    }
    else{
        $agent = "";
        header("Location: register.php?msg=$agent");
    }

?>