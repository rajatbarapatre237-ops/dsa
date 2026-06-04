<?php 
    include "connect/db.php";
    include "connect/fun.php";
    include 'include/auth_session.php';
  
    $connect = new connect();
    $fun=new fun($connect->dbconnect());
  
    //$fetch = $fun->fetchStudents();
    if(isset($_GET['stud'])){
        
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $verified = !$_GET['verify'];
                    
            $verify = $fun->updateStudentstatus($id,$verified);
            echo $verified;
            header("Location: view-student.php");
        }
        else{
            header("Location: view-student.php");

        }
    }
    else if(isset($_GET['session'])){
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $verify = $_GET['verify'];
            $fun->updateSessionStatus($id, $verify);
            header("Location: view_sessions.php");
        } else {
            header("Location: view_sessions.php");
        }
    }
    else if(isset($_GET['batch'])){
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $verify = $_GET['verify'];
                    
            $verify = $fun->updateBatchStatus($id,$verify);
            header("Location: view_batches.php");
        }
        else{
            header("Location: view_ batches.php");

        }
    }
    else if(isset($_GET['course'])){
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $verify = $_GET['verify'];
                    
            $verify = $fun->verifyCourse($id,$verify);
            echo $verify;
            //header("Location: view-course.php");
        }
        else{
            //header("Location: view-course.php");

        }
    }
    else if(isset($_GET['internship'])){
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $verify = !$_GET['verify'];
                    
            $verify = $fun->updateInternshipsStatus($id,$verify);
            header("Location: view_internships.php");
        }
        else{
            header("Location: view_internships.php");

        }
    }
    else if(isset($_GET['interns'])){
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $verify = !$_GET['verify'];
                    
            $verify = $fun->updateInternsStatus($id,$verify);
            header("Location: view_interns.php");
        }
        else{
            header("Location: view_interns.php");

        }
    }
    else if(isset($_GET['assignment'])){
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $verify = !$_GET['verify'];
                    
            $verify = $fun->updateAssignmentStatus($id,$verify);
            echo $verify;
            //header("Location: view_assignment.php");
        }
        else{
            //header("Location: view_assignment.php");

        }
    }
    else{
        //header("Location: index.php");
    }
    

?>