<?php 
    include "connect/db.php";
    include "connect/fun.php";
    include 'include/auth_session.php';

    $connect = new connect();
    $fun = new fun($connect->dbconnect());
    if(isset($_GET['course'])){
        $id = $_GET['id'];
        $delete = $fun->deleteCourse($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view-course.php?msg=$msg");


    }
  
    else if(isset($_GET['internship'])){
        $id = $_GET['id'];
        $delete = $fun->deleteInternshipById($id);
        if($delete){
            $msg = "Deleted";   
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_internships.php?msg=$msg");

    }
    else if(isset($_GET['tid'])){
        $id = $_GET['tid'];
        $delete = $fun->deletesalaryhistory($id);
        if($delete){
            $msg = "Deleted";   
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: salary_history.php?msg=$msg");

    }
    else if(isset($_GET['Reginterns'])){
        $id = $_GET['id'];
        $delete = $fun->deleteInternById($id);
        if($delete){
            $msg = "Deleted";   
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: add_interns.php?msg=$msg");

    }
    else if(isset($_GET['interns'])){
        $id = $_GET['id'];
        $delete = $fun->deleteWorkingInternById($id);
        if($delete){
            $msg = "Deleted";   
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_interns.php?msg=$msg");

    }

    else if(isset($_GET['teacher'])){
        $id = $_GET['id'];
        $delete = $fun->deleteTeacher($id);
        if($delete){
            $msg = "Deleted";   
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_teacher.php?msg=$msg");
    }
    else if(isset($_GET['student'])){
        $id = $_GET['id'];
        $delete = $fun->deleteStudDetails($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view-student.php?msg=$msg");

    }
    // else if(isset($_GET['id'])){
    //     $id = $_GET['id'];
    //     $delete = $fun->deleteBatch($id);
    //     if($delete){
    //         $msg = "Deleted";
    //     }
    //     else{
    //         $msg = "Not Deleted";
    //     }
    //     header("Location: view_batches.php?msg=$msg");

    // }
    // else if(isset($_GET['id'])){
    //     $id = $_GET['id'];
    //     $delete = $fun->deleteassignment($id);
    //     if($delete){
    //         $msg = "Deleted";
    //     }
    //     else{
    //         $msg = "Not Deleted";
    //     }
    //     header("Location: view_assignment.php?msg=$msg");

    // }
    else if(isset($_GET['Regstudent'])){
        $id = $_GET['id'];
        $delete = $fun->deleteRegStud($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_reg_students.php?msg=$msg");

    }
     else if(isset($_GET['assignment'])){
        $id = $_GET['id'];
        $delete = $fun->deleteAssignment($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_assignment.php?msg=$msg");

    }
    else{
        $msg = "Invalid";
    }

?>