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
    else if(isset($_GET['session']) && isset($_GET['id'])){
        $id = (int) $_GET['id'];
        $result = $fun->deleteAcademicSession($id);
        $msg = urlencode($result['message'] ?? ($result['ok'] ? 'Deleted' : 'Not deleted'));
        header("Location: view_sessions.php?msg=" . $msg);
        exit;
    }
    else if(isset($_GET['batch']) && isset($_GET['id'])){
        $id = $_GET['id'];
        $delete = $fun->deleteBatch($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_batches.php?msg=$msg");

    }
    else if(isset($_GET['id'])){
        $id = $_GET['id'];
        $delete = $fun->deleteassignment($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_assignment.php?msg=$msg");

    }
    else if(isset($_GET['Regstudent'])){
        $id = $_GET['studid'];
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
    else if(isset($_GET['sliderone'])){
        $id = $_GET['img_id'];
        $delete = $fun->deletesliderone($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_slider_one.php?msg=$msg");

    }
    else if(isset($_GET['slidertwo'])){
        $id = $_GET['twoimg_id'];
        $delete = $fun->deleteslidertwo($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_slider_two.php?msg=$msg");

    }
    else if(isset($_GET['teacherupload'])){
        $id = $_GET['teacher_id'];
        $delete = $fun->deleteteacherupload($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_teachers_upload.php?msg=$msg");

    }
    else if(isset($_GET['subject_id'])){
        $id = $_GET['subject_id'];
        $delete = $fun->deletetesubject($id);
        if($delete){
            $msg = "Deleted";
        }
        else{
            $msg = "Not Deleted";
        }
        header("Location: view_subject.php?msg=$msg");

    }
   
    else{
        $msg = "Invalid";
    }

?>