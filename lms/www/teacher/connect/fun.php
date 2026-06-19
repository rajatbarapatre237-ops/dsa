<?php

class fun
{
    private $db;
    function __construct($con)
    {
        $this->db = $con;

    }

    // public function login($username,$password){
        
    //     $query    = "SELECT * FROM `admin` WHERE `username`='$username' AND `password` = '$password'";
    //     $result = mysqli_query($this->db, $query);

        
    //     $rows = mysqli_num_rows($result);
    //     if ($rows == 1) {
           
            
    //             return ["Done",1];
            
           
    //         // Redirect to user dashboard page
           
             
    //     }
    //     else{
    //         return ["Failed",0];
    //     }
    // }
    public function login($email,$password)
    {
        
            $sql = "SELECT `email`, `password` FROM `teacher` WHERE `email` = '$email' AND `password` = '$password'";
            
            $fetch = mysqli_query($this->db, $sql);
            
             return $fetch;
       }

    public function fetchStudent($course){
        $sql = "SELECT * FROM `stud_details` WHERE `course_name`='$course'";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
   }


   public function fetchAllCourses(){
    $sql = "SELECT * FROM `course_details` ORDER BY `id` DESC";
    $fetch = mysqli_query($this->db, $sql);
     return $fetch;
   }
   public function fetchcoursebyemail($email){
    $sql = "SELECT * FROM `teacher` WHERE `email`='$email'";
    $courseee = mysqli_query($this->db, $sql);
     return $courseee;
   }
   public function fetchcoursebyemail2($email){
    $sql = "SELECT * FROM `course_assign` WHERE `email`='$email'";
    $courseee = mysqli_query($this->db, $sql);
     return $courseee;
   }
   public function fetchcoursedistinct($email){
    return $this->fetchTeacherCourses($email);
   }

    public function fetchAllStudents()
    {
        $sql = "SELECT * FROM `stud_details`  ORDER BY `id` DESC";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function teacherSalary()
    {
        $sql = "SELECT * FROM `teacher` ORDER BY `course` DESC";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }



    public function checkTeacherExists($email){
        $sql = "SELECT * FROM `teacher` WHERE `email` = '$email'";
        $fetch = mysqli_query($this->db, $sql);
        if(mysqli_num_rows($fetch) == 0){
             return true;
        }
        else{
             return false;
        }
       }

       public function deleteTeacher($id){
        $sql = "DELETE FROM `teacher` WHERE `tid` = $id";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
       }
       public function fetchAllTeachers(){
        $sql = "SELECT * FROM `teacher`";
        $fetch = mysqli_query($this->db, $sql); 
        return $fetch;
       }
       public function fetchTeacherWithId($id){
        $sql = "SELECT * FROM `teacher` WHERE `tid` = '$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
       }
       public function fetchTeacherWithemail($email){
        $sql = "SELECT * FROM `teacher` WHERE `email` = '$email'";
        $teach = mysqli_query($this->db, $sql);
        return $teach;
       }
       public function fetchCourseWithemail($email){
        $sql = "SELECT * FROM `courses_subjects` WHERE `teacher_email` = '$email'";
        $cour = mysqli_query($this->db, $sql);
        return $cour;
       }
       public function fetchSubjectwithCourse($cour){
        $sql = "SELECT `subject_name` FROM `subject` WHERE `course_name` = '$cour'";
        $sub = mysqli_query($this->db, $sql);
        return $sub;
       }
       public function updatepass($prevpass,$newpass){
        $sql = "UPDATE `teacher` SET `password`='$newpass' WHERE `password`='$prevpass'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
       }


       public function updateTeacherById($id,$name,$email,$phone,$salary,$course){
        $sql = "UPDATE `teacher` SET `name`='$name',`email`='$email',`phone`='$phone',`salary`='$salary',`course`='$course' WHERE `tid`='$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
       }

       public function assignedTeacherWithId($id){
        $sql = "SELECT * FROM `teacher_course` WHERE `tid` = '$id' ORDER BY `tid` DESC";
        $fetch = mysqli_query($this->db, $sql);
        
        return $fetch;
       }
    public function addTeacher($name,$email,$phone,$salary,$course){
        $sql = "INSERT INTO `teacher`( `name`, `email`, `phone`, `salary`,`course`) VALUES ('$name','$email','$phone','$salary','$course')";
        $fetch = mysqli_query($this->db, $sql);
        
             return $fetch;
       }
       public function teacherpaysalary($tid, $name, $course, $salary, $formatted_date){
        $sql = "INSERT INTO `teacher_salary`( `tid`, `name`, `course`, `salary`,`date`) VALUES ('$tid','$name','$course','$salary','$formatted_date')";
        $fetch = mysqli_query($this->db, $sql);
        
             return $fetch;
       }

       public function fetchteachersalary($tid)
       {
           $sql = "SELECT * FROM `teacher_salary` WHERE `tid` = '$tid' ";
           $fetch = mysqli_query($this->db, $sql);
           return $fetch;
       }
       public function fetchteachersalarybyemail($email)
       {
           $sql = "SELECT * FROM `teacher_salary` WHERE `email` = '$email' ";
           $fetch = mysqli_query($this->db, $sql);
           return $fetch;
       }


    public function fetchRegStudents()
    {
        $sql = "SELECT * FROM `registered_stud`  ORDER BY `id` DESC";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    
    public function fetchAllUsers()
    {
        $sql = "SELECT * FROM `users` ORDER BY `sr_no` DESC";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function fetchCourseWithname($name)
    {
        $sql = "SELECT * FROM `course_details` WHERE `course_name`= '$name'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function insertStudentDetails($POST, $FILE)
    {
        $target_dir = "student_pfp/";
        $pfp = $target_dir . basename($FILE["pfp"]["name"]);
        $pfpType = strtolower(pathinfo($pfp, PATHINFO_EXTENSION));
        $pfpname = $POST['name'] . "_PFP" . ".$pfpType";
        $pfp = $target_dir . $pfpname;
        //echo "$pfpname";
        $name = $POST['name'];
        $age = $POST['age'];
        $email = $POST['email'];
        $city = $POST['city'];
        $state = $POST['state'];
        $mobile = $POST['mobile'];
        $school = $POST['school'];
        $course = $POST['course'];
        $batch = $POST['batch'];
        $c = $this->fetchCourseWithname($course);
        $co = mysqli_fetch_assoc($c);

        $fees = $co['course_fees'];
        $aadhar = $POST['aadhar'];
        


        $sql = "INSERT INTO `stud_details`(`name`,`pfp`,`age`, `mobile`, `school_name`, `email`, `city`, `state`, `course_name`, `course_fees`,`balance_fees`,`batch`, `aadhar`,`date_of_joining`) 
        VALUES ('".$name."','".$pfpname."','".$age ."','".$mobile."','".$school."','$email' ,'$city','$state','". $course."','" . $fees . "','" . $fees . "',' $batch','" . $aadhar . "',curdate())";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            move_uploaded_file($FILE["pfp"]["tmp_name"], $pfp);

        }
        return $query;
    }

    public function updateStudentDetail($POST)
    {
        $id = $POST['id'];
        $stud = $this->getStudentByID($id);
        $student = mysqli_fetch_assoc($stud);
        $PaidFees = $student['course_fees'] - $student['balance_fees'];
        $name = $POST['name'];
        $age = $POST['age'];
        $mobile = $POST['mobile'];
        $batch = $POST['batch'];
        $school = $POST['school'];
        $cid = $POST['course'];
        $c = $this->fetchCourseWithId($cid);
        $co = mysqli_fetch_assoc($c);
        $course = $co['course_name'];
        $fees = $POST['fees'];
        $aadhar = $POST['aadhar'];
        $date = ($POST['date']!=null)?($POST['date']):(date("d-m-Y"));
        $balance = $fees - $PaidFees;

        
        $sql = "UPDATE `stud_details` SET `name`='" . $name . "',`age`='" . $age . "',`mobile`='" . $mobile . "',`school_name`='" . $school . "',`course_name`='" . $course . "',`course_fees`='" . $fees . "',`balance_fees`='" . $balance . "',`aadhar`='" . $aadhar . "',`date_of_joining`='" . $date . "',`batch` ='" . $batch . "' WHERE `id`='" . $id . "'";
        $fetchh = mysqli_query($this->db, $sql);
        if($fetchh){
            $this->updateCourseTakenLatest($id,$cid,$fees);
        }
        return $fetchh;
    }

    public function insertStudentDetailMain($POST, $FILE)
    {
        $target_dir = "admin/student_pfp/";
        $pfp = $target_dir . basename($FILE["pfp"]["name"]);
        $pfpType = strtolower(pathinfo($pfp, PATHINFO_EXTENSION));
        $pfpname = $POST['name'] . "_PFP" . ".$pfpType";
        $pfp = $target_dir . $pfpname;
        //echo "$pfpname";
        $name = $POST['name'];
        $age = $POST['age'];
        $email = $POST['email'];
        $city = $POST['city'];
        $state = $POST['state'];
        $mobile = $POST['mobile'];
        $school = $POST['clg'];
        $course = $POST['course'];
        $batch = $POST['batch'];
        $c = $this->fetchCourseWithname($course);
        $co = mysqli_fetch_assoc($c);
       
        $fees = $co['course_fees'];
        $aadhar = $POST['aadhar'];
        


        $sql = "INSERT INTO `registered_stud`(`name`,`pfp`,`age`, `mobile`, `school_name`, `email`, `city`, `state`, `course_name`, `course_fees`,`balance_fees`,`batch`, `aadhar`,`date_of_joining`) 
                VALUES ('".$name."','".$pfpname."','".$age ."','".$mobile."','".$school."','$email' ,'$city','$state','". $course."','" . $fees . "','" . $fees . "','" . $aadhar . "',curdate())";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            move_uploaded_file($FILE["pfp"]["tmp_name"], $pfp);

        }
        return $query;
    }
    
    

    public function lastFiveStudents()
    {
        $sql = "SELECT * FROM stud_details ORDER BY id DESC LIMIT 5";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function getAllInternships()
    {
        $sql = "SELECT * FROM `internships` ";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }
    public function getAllInternshipsWithId($id)
    {
        $sql = "SELECT * FROM `internships` WHERE `id`= '$id' ";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function updateBatch($POST)
    {
        $id = $POST['id'];
        $name = $POST['name'];
        $start = $POST['start'];
        $end = $POST['end'];
        $sql = "UPDATE `batches` SET `name`='$name',`start_time`='$start',`end_time`='$end' WHERE `id`='$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function insertContactDetails($name, $email, $message)
    {
        $sql = "INSERT INTO `contact_us`( `name`, `email`, `message`) VALUES ('" . $name . "','" . $email . "','" . $message . "')";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function insertCourses($POST)
    {
        $cname = $POST['courseName'];
        $fees = $POST['fees'];
        $sql = "INSERT INTO `course_details`( `course_name`, `course_fees`) VALUES ('" . $cname . "','" . $fees . "')";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }
    public function getCourseDetails()
    {
        $sql = "SELECT * FROM `course_details` ORDER BY `id` DESC";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function getTotalBalanceFees()
    {
        $total_remaining = "SELECT SUM(balance_fees) AS balance FROM stud_details;";
        $remain = mysqli_query($this->db, $total_remaining);
        $row = mysqli_fetch_assoc($remain);
        return $row['balance'];
    }

    public function fetchBatchById($id)
    {
        $sql = "SELECT * FROM `batches` WHERE `id` = $id";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function updateStudentstatus($id, $verify)
    {

        $sql = "UPDATE `stud_details` SET `status`='$verify' WHERE id=" . $id . "";


        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function updateCourse($POST)
    {
        $course = $POST['cname'];
        $fees = $POST['fees'];
        $id = $POST['id'];
        $sql = "UPDATE `course_details` SET `course_name`='$course',`course_fees`='$fees' WHERE `id`='$id'";
        $fetch = mysqli_query($this->db, $sql);

        return $fetch;
    }

    public function fetchCourseWithId($id)
    {
        $sql = "SELECT * FROM `course_details` WHERE `id`= '$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function deleteCourse($id)
    {
        $sql = "DELETE FROM `course_details` WHERE `id` = $id";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function verifyCourse($id, $verify)
    {
        $verify = !$verify;
        $sql = "UPDATE `course_details` SET `status`='$verify' WHERE `id`='$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;

    }

   
    
    
    
    public function fetchCoursesTaken($sid){
        $sql = "SELECT * FROM courses_taken WHERE `sid` = '$sid'";
        $fetch = mysqli_query($this->db,$sql);
        $fetch = mysqli_fetch_assoc($fetch);
        return $fetch;
    }

        public function updateCourseTakenLatest($sid,$cid,$fees){
            $fetch = $this->fetchCoursesTaken($sid);
            $courses = explode(",",$fetch['courses']);
            $size = sizeof($courses);
            $courses[$size-1] = $cid;
            $course = implode(",",$courses);
            $fees = explode(",",$fetch['course_fees']);
            $sizef = sizeof($fees);
            $fees[$sizef-1] = $fees;
            $fee = implode(",",$fees);
            $sql = "UPDATE `courses_taken` SET `courses`='$course',`current_course`='$cid',`course_fees`='$fee' WHERE `sid`='$sid'";
            $fetch = mysqli_query($this->db,$sql);
            return $fetch;
    }
    public function getWorkingInternsById($id)
    {
        $sql = "SELECT * FROM working_interns where id =" . $id . ";";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }
    public function getAllWorkingInterns()
    {
        $sql = "SELECT * FROM working_interns where 1;";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function updateWorkingIntern($cid, $id)
    {
        $add = "UPDATE `working_interns` SET `CID`='" . $cid . "' WHERE `id`='" . $id . "'";
        mysqli_query($this->db, $add);
    }

    public function getWorkingWithLimit($start, $limit)
    {
        $fetch = "SELECT * FROM working_interns LIMIT $start, $limit";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function updateWorkingStatus($id, $status = 0)
    {
        $intern = "UPDATE `working_interns` SET `status`= '" . $status . "' WHERE `id`='" . $id . "'";
        $result = mysqli_query($this->db, $intern);
        return $result;
    }

    public function getInternsByID($POST)
    {
        $id1 = $POST['id'];
        $id = substr($id1, 3);
        $sql = "SELECT * FROM intern where id =" . $id . ";";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function getInternWithLimit($start, $limit)
    {
        $fetch = "SELECT * FROM intern LIMIT $start, $limit";
        $query = mysqli_query($this->db, $fetch);
        return $query;
    }

    public function getAllIntern()
    {
        $sql = "SELECT * FROM intern ";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function deleteInternById($id)
    {
        $delete = "DELETE FROM `intern` WHERE id =" . $id . "";
        $query = mysqli_query($this->db, $delete);
        return $query;
    }
    public function deleteWorkingInternById($id)
    {
        $delete = "DELETE FROM `working_interns` WHERE id =" . $id . "";
        $query = mysqli_query($this->db, $delete);
        return $query;
    }
    public function deleteInternshipById($id)
    {
        $delete = "DELETE FROM `internships` WHERE id ='" . $id . "'";
        $query = mysqli_query($this->db, $delete);
        return $query;
    }

    public function transferIntern($id)
    {
        $intern = "INSERT INTO working_interns(`id`, `name`, `email`, `gender`, `phone_no.`, `city`, `state`, `clg_name`, `i_domain`, `resume`)
            SELECT `id`, `name`, `email`, `gender`, `phone_no.`, `city`, `state`, `clg_name`, `i_domain`, `resume` FROM intern
            WHERE `id` = '" . $id . "';";
        $query = mysqli_query($this->db, $intern);
        if ($query) {
            $this->deleteInternById($id);
        }
        return $query;
    }

    public function deleteRegStud($id)
    {
        $stud = $this->fetchRegStudentsWithId($id);
        $student = mysqli_fetch_assoc($stud);
        $pfp = $student['pfp'];
        $sql = "DELETE FROM `registered_stud` WHERE `id` = $id";
        $fetch = mysqli_query($this->db, $sql);
        if($fetch){
            unlink("student_pfp/".$pfp);
        }
        return $fetch;

    }
    
    public function deleteTransferRegStud($id)
    {
        
        $sql = "DELETE FROM `registered_stud` WHERE `id` = $id";
        $fetch = mysqli_query($this->db, $sql);
        
        return $fetch;

    }

    public function fetchRegStudentsWithId($id)
    {
        $sql = "SELECT * FROM `registered_stud` WHERE `id` = $id";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function transferRegStud($id)
    {
        
        
        $regstud = $this->fetchRegStudentsWithId($id);
        $reg = mysqli_fetch_assoc($regstud);
        $course = $this->fetchCourseWithId($reg['course_name']);
        $courses = mysqli_fetch_assoc($course);
        $cid = $reg['course_name'];
        
       
        $studCourse = $this->fetchStudentCourseTaken($id);
        $stud = $this->getStudentByID($id);
        $student = mysqli_fetch_assoc($stud);
        $fees = $reg['course_fees'];
        if(mysqli_num_rows($studCourse)){
            $studC = mysqli_fetch_assoc($studCourse);
            $courses = $studC['courses'].",".$cid;
            $fee = $studC['course_fees'].",".$fees;
            $taken_course_sql = "UPDATE `courses_taken` SET `courses`='$courses',`current_course`='$cid',`course_fees` = '$fee' WHERE `sid`='$id';";
        }
        else{
            
            $taken_course_sql = "INSERT INTO `courses_taken`( `sid`, `courses`, `current_course`,`course_fees`) VALUES ('$id','$cid','$cid','$fees')";
        }
        
        $intern = "INSERT INTO `stud_details`(`id`, `name`, `pfp`, `age`, `mobile`, `school_name`, `email`, `city`, `state`, `course_name`, `course_fees`, `balance_fees`, `aadhar`, `date_of_joining`)
                     VALUES ('$id','".$reg['name']."','".$reg['pfp']."','".$reg['age']."','".$reg['mobile']."','".$reg['school_name']."','".$reg['email']."','".$reg['city']."','".$reg['state']."','".$courses['course_name']."','".$courses['course_fees']."','".$courses['course_fees']."','".$reg['aadhar']."','".$reg['date_of_joining']."')";
        $query = mysqli_query($this->db, $intern);
        if ($query) {
            $fetch = mysqli_query($this->db,$taken_course_sql);
            $this->deleteTransferRegStud($id);
        }
        return $query;
    }


    public function insertInternships($POST)
    {
        $name = $POST['name'];

        $type = $POST['type'];
        $duration = $POST['duration'];
        $perks = $POST['perks'];
        $skills = $POST['skills'];
        $sql = "INSERT INTO `internships`( `name`, `type`, `duration`, `perks`, `skills`, `status`) VALUES ('" . $name . "','" . $type . "','" . $duration . "','" . $perks . "','" . $skills . "',1)";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function updateInternshipsStatus($id, $status)
    {
        $sql = "UPDATE `internships` SET
                            `status`='" . $status . "' WHERE id='" . $id . "';";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }
    public function updateInternsStatus($id, $status)
    {
        $sql = "UPDATE `working_interns` SET
                            `status`='" . $status . "' WHERE id='" . $id . "';";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function updateWorkingInterns($POST)
    {
        $id = $POST['id'];
        $name = $POST['name'];
        $email = $POST['email'];
        $gender = $POST['gender'];
        $phone = $POST['phone'];
        $city = $POST['city'];
        $state = $POST['state'];
        $clg = $POST['clg'];
        $internship = $POST['domain'];
        $sql = "UPDATE `working_interns` SET `name`='$name',`email`='$email',`gender`='$gender',`phone_no.`='$phone',`city`='$city',`state`='$state',`clg_name`='$clg',`i_domain`='$internship' WHERE `id`='$id'";
        $ftech = mysqli_query($this->db, $sql);
        return $ftech;
    }

    public function updateInternships($POST)
    {
        $name = $POST['name'];
        $type = $POST['type'];
        $duration = $POST['duration'];
        $perks = $POST['perks'];
        $skills = $POST['skills'];
        $id = $POST['id'];
        $sql = "UPDATE `internships` SET `name`='$name',`type`='$type',`duration`='$duration',`perks`='$perks',`skills`='$skills' WHERE `id`='$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }



    public function getStudentByID($id)
    {
        $sql = "SELECT * FROM stud_details where id ='" . $id . "';";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }

    public function getStudentWithLimit($start, $limit)
    {
        $fetch = "SELECT * FROM stud_details LIMIT $start, $limit";

        $result = mysqli_query($this->db, $fetch);
        return $result;
    }
    public function getStudentWithLimitAndBatch($start, $limit, $batch)
    {
        $fetch = "SELECT * FROM stud_details  where  `batch` = '" . $batch . "' LIMIT $start, $limit;";

        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function countTotalStudents()
    {
        $user = "SELECT COUNT(*) as count_stud FROM stud_details ";
        $count = mysqli_query($this->db, $user);
        $assoc = mysqli_fetch_assoc($count);
        return $assoc['count_stud'];
    }
    
    public function deleteCourseTaken($sid){
        $sql = "DELETE FROM `courses_taken` WHERE `sid` = $sid";
        $fetch = mysqli_query($this->db,$sql);
        
    }
    public function deleteStudDetails($id)
    {
       $stud = $this->getStudentByID($id);
        $student = mysqli_fetch_assoc($stud);
        $pfp = $student['pfp'];
        $delete = "DELETE FROM `stud_details` WHERE id =" . $id . "";
        $result = mysqli_query($this->db, $delete);
        if($result){
            unlink("student_pfp/".$pfp);
            $this->deleteCourseTaken($id);
            
        }
        return $result;
    }
    
    public function deleteUsers($sid){
        $sql = "DELETE FROM `users` WHERE `sid`='$sid'";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
    }

    public function filterStudentWithBatch($id, $batch)
    {
        $fetch = "SELECT * FROM stud_details where id =" . $id . " and `batch` = " . $batch . ";";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }
    public function fetchAllEarnings(){
        $sql = "SELECT * FROM `earning` ORDER BY `id` DESC";
        $fetch = mysqli_query($this->db,$sql);
        
        return $fetch;
    }
    public function getEarning()
    {
        $month = date('m');
        $year = date('y');
        $date = "".$month."-".$year."";
        $amount = "SELECT * FROM `earning` WHERE `month_year`='$date' ORDER BY id DESC LIMIT 1 ";
        $am = mysqli_query($this->db, $amount);
        if($am){
            
        $earn = mysqli_fetch_assoc($am);
        $earning = $earn['earning'];
        }
        else{
            $earning = 0;
        }
        return $earning;
    }

    public function getLastInternId()
    {
        $last = "SELECT * FROM intern ORDER BY id DESC LIMIT 1";
        $record = mysqli_query($this->db, $last);
        $res = mysqli_fetch_assoc($record);
        $num_rows = mysqli_num_rows($record);
        return $num_rows;
    }

    public function countInterns()
    {
        $user = "SELECT COUNT(*) as count_intern FROM intern ";
        $count = mysqli_query($this->db, $user);
        $assoc = mysqli_fetch_assoc($count);
        return $assoc['count_intern'];
    }

    public function insertIntern($POST, $FILE)
    {
        $name = $POST['name'];
        $email = $POST['email'];
        $gender = $POST['gender'];
        $phone = $POST['phone'];
        $city = $POST['city'];
        $state = $POST['state'];
        $clg = $POST['clg'];
        $domain = $POST['domain'];
        $intern = $this->getAllInternshipsWithId($domain);
        $internships = mysqli_fetch_assoc($intern);
        $domain = $internships['name'];
        $target_dir = "./uploads/";
        $resume = $target_dir . basename($FILE["resume"]["name"]);
        $resumeType = strtolower(pathinfo($resume, PATHINFO_EXTENSION));
        $resumename = $POST['name'] . "_Resume" . ".$resumeType";
        $resume = $target_dir . $resumename;
        $sql = "INSERT INTO intern ( `name`, `email`, `gender`, `phone_no.`, `city`, `state`, `clg_name`,`i_domain`,     `resume`) VALUES ('" . $name . "','" . $email . "','" . $gender . "','" . $phone . "','" . $city . "','" . $state . "','" . $clg . "','" . $domain . "','" . $resumename . "')";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            move_uploaded_file($FILE["resume"]["name"], $resume);
        }
        return $query;
    }

    public function insertBatches($POST)
    {
        $course = $POST['course'];
        $name = $POST['name'];
        $start = $POST['start'];
        $end = $POST['end'];
        $sql = "INSERT INTO `batches`(`course`,`name`, `start_time`, `end_time`, `status`) VALUES ('" . $course . "','" . $name . "','" . $start . "','" . $end . "','1')";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function updateBatchStatus($id, $verify)
    {
        $status = !$verify;
        $sql = "UPDATE `batches` SET
                            `status`='" . $status . "' WHERE id='" . $id . "';";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function getAllBatches()
    {
        $fetch = "SELECT * FROM batches ORDER BY `id` DESC";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function deleteBatch($id)
    {
        $sql = "DELETE FROM `batches` WHERE `id` = '$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function updateBalanceFees($balance, $id)
    {
        $deposit = "UPDATE `stud_details` SET `balance_fees`='" . $balance . "' WHERE `id` = " . $id . "";
        $result = mysqli_query($this->db, $deposit);
        return $result;
    }

    public function insertTransactionHistory($id, $name, $remain, $reason,$date)
    {
        $transaction = "INSERT INTO `transaction_history`( `user_id`, `name`, `amount`, `reason`, `date`) VALUES ('" . $id . "','" . $name . "','" . $remain . "','" . $reason . "','$date')";
        mysqli_query($this->db, $transaction);
    }

    public function getTransactionWithId($id)
    {
        $fetch = "SELECT * FROM transaction_history where id ='" . $id . "';";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function fetchTransactionHistory()
    {
        $sql = "SELECT * FROM transaction_history  ORDER BY `date` DESC";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function getTransactionWithLimit($start, $limit)
    {
        $fetch = "SELECT * FROM transaction_history ORDER BY `id` DESC  LIMIT $start, $limit";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function getLastEarningMonth()
    {
        $amount = "SELECT * FROM `earning` ORDER BY `id` DESC LIMIT 1 ";
        $am = mysqli_query($this->db, $amount);
        return $am;
    }

    public function insertEarning($remain, $date)
    {
        $earning = "INSERT INTO `earning`( `earning`, `month_year`) VALUES ('" . $remain . "','" . $date . "')";
        $am = mysqli_query($this->db, $earning);
        return $am;
    }

    public function updateEarning($remain, $id)
    {
        $earning = "UPDATE `earning` SET `earning`='" . $remain . "' WHERE `id`='" . $id . "'";
        mysqli_query($this->db, $earning);
    }

    public function fetchStudentCourseTaken($id){
        $sql  = "SELECT * FROM courses_taken WHERE `sid`='$id'";
        $fetch = mysqli_query($this->db,$sql);

        return $fetch;
    }
    


    public function updateCourseStudent($POST){
        $cid = $POST['course'];
        $sid = $POST['id'];
        $course = $this->fetchCourseWithId($cid);
        $course_detail = mysqli_fetch_assoc($course);
        $studCourse = $this->fetchStudentCourseTaken($sid);
        $stud = $this->getStudentByID($sid);
        $student = mysqli_fetch_assoc($stud);
        $fee = $course_detail['course_fees'];
        if(mysqli_num_rows($studCourse)){
            $studC = mysqli_fetch_assoc($studCourse);
            $courses = $studC['courses'].",".$cid;
            $fees = $studC['course_fees'].",".$fee;
            $taken_course_sql = "UPDATE `courses_taken` SET `courses`='$courses',`current_course`='$cid',`course_fees`='$fees' WHERE `sid`='$sid';";
        }
        else{
            
            $taken_course_sql = "INSERT INTO `courses_taken`( `sid`, `courses`, `current_course`,`course_fees`) VALUES ('$sid','$cid','$cid','$fee')";
        }
        $fees = $student['course_fees'] + $course_detail['course_fees'];
        $balance = $student['balance_fees'] + $course_detail['course_fees'];
        $sql = "UPDATE `stud_details` SET `batch`='none', `course_name`='".$course_detail['course_name']."',`course_fees`='$fees',`balance_fees`='$balance' WHERE `id`='$sid'";

        $courseTaken = mysqli_query($this->db,$taken_course_sql);
        $st = mysqli_query($this->db,$sql);
        return [$courseTaken,$st];
    }
    
    
    #new
     public function insertAssignment($POST,$FILES){
         $batch = $POST['batch'];
        $dname = $POST['dname'];
        $type = $POST['select'];
        if($type == "link"){
            $link = $POST['link'];
            $sql = "INSERT INTO `assignement`( `batch_name`,`type`, `document_name`,`document`) VALUES ('$batch','$type','$dname','$link')";

        }
        else{
            $target_dir = "documents/";
            $date = Date("d-m-Y");
            $pfp = $target_dir . basename($FILES["assignment"]["name"]);
            $pfpType = strtolower(pathinfo($pfp, PATHINFO_EXTENSION));
            $pfpname = $dname . "_$date" . ".$pfpType";
            $pfp = $target_dir . $pfpname;

            $sql = "INSERT INTO `assignement`( `batch_name`,`type`, `document_name`,`document`) VALUES ('$batch','$type','$dname','$pfpname')";

        }
        $fetch = mysqli_query($this->db,$sql);
        if($fetch && $type == "file"){
            move_uploaded_file($FILES["assignment"]["tmp_name"], $pfp);
        }
        return $fetch;


    }
    public function updateAssignmentStatus($id,$status){
        $sql = "UPDATE `assignement` SET `status`='$status' WHERE id=" . $id . "";


        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function deleteAssignment($id){
        $assignment = $this->fetchAssignmentWithId($id);
        $assign  = mysqli_fetch_assoc($assignment);
        $sql = "DELETE FROM `assignement` WHERE `id` = '$id'";
        $fetch = mysqli_query($this->db,$sql);
        if($fetch){
            unlink("documents/".$assign['document']);
        }
        return $fetch;
    }

    public function fetchAllAssignment(){
        $sql = "SELECT * FROM `assignement` ORDER BY `id` DESC";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
    }
    
    public function fetchAssignmentWithId($id){
        $sql = "SELECT * FROM `assignement` WHERE `id` = $id";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
    }
    
    
     public function getEarningData(){
        $earn = [0,0,0,0,0,0,0,0,0,0,0,0];
        $year = Date("Y");
        $year = substr($year,2);
        $sql = "SELECT `earning`,`month_year` FROM `earning` WHERE `month_year` LIKE '%$year'";
        $fetch = mysqli_query($this->db,$sql);
        if(mysqli_num_rows($fetch)>0){
            while($res = mysqli_fetch_assoc($fetch)){
                $month = substr($res['month_year'],0,2);
                //echo "Month:- $month";
                $month = intval($month);
                $earn[$month-1] = $res['earning'];
            }
        }
        //print_r($earn);
        return $earn;
    }

    public function getAddmissionReport(){
        $add = [0,0,0,0,0,0,0,0,0,0,0,0];
        $year = Date("Y");
        for($i = 1; $i<=12;$i++){
            $m = ($i<10)?("0".$i):($i);
            $sql = "SELECT COUNT(*) AS `count` FROM `stud_details` WHERE `date_of_joining` LIKE '$year-$m%'";
            $fetch = mysqli_query($this->db,$sql);
            $fetch  = mysqli_fetch_assoc($fetch);
            //echo $fetch['count']."<br>";
            $add[$i-1] = $fetch['count'];
        }
        //print_r($add);
        return $add;
    }

    

    public function getCourseStudentData(){
        $arr = [];
        $sql = "SELECT `course_name`, COUNT(`course_name`) as count FROM `stud_details` GROUP BY `course_name` HAVING COUNT(`course_name`) > 1;";
        $fetch = mysqli_query($this->db,$sql);
        if(mysqli_num_rows($fetch)>0){
            while($res = mysqli_fetch_assoc($fetch)){
                $arr[$res['course_name']] = $res['count'];
            }
        }
        return $arr;
    }

    
    public function getTotalSum() {
        $sql = "SELECT SUM(`course_fees`) AS sum FROM `stud_details` WHERE 1";
        $fetch = mysqli_query($this->db,$sql);
        $fetch = mysqli_fetch_assoc($fetch);
        return $fetch['sum'];
    }
    public function fetchLastFiveTransaction()
    {
        $sql = "SELECT * FROM transaction_history  ORDER BY id DESC LIMIT 5";
        $fetch = mysqli_query($this->db, $sql);
        
        return $fetch;
    }
    public function getBalanceStudentsWithBatch($batch){
        $sql = "SELECT * FROM `stud_details` WHERE  `batch` ='$batch' AND `balance_fees`> 0 ORDER BY `balance_fees` DESC;";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
    }

    public function getBatchWithId($id){
        $sql = "SELECT `name` FROM `batches` WHERE `id` = '$id'";
        $fetch = mysqli_query($this->db,$sql);
        $fetch = mysqli_fetch_assoc($fetch);
        return $fetch['name'];
    }
    public function fetchassigncourse($email){
        return $this->fetchTeacherCourses($email);
    }

    /** Courses assigned via courses_subjects or course_assign. */
    public function fetchTeacherCourses($email)
    {
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT DISTINCT course_name AS course FROM courses_subjects WHERE teacher_email = '$email'
                UNION
                SELECT DISTINCT course AS course FROM course_assign WHERE email = '$email'
                ORDER BY course ASC";
        return mysqli_query($this->db, $sql);
    }
    public function fetchbatchwithcourse($course){
        $sql = "SELECT `name` FROM `batches` WHERE `course`= '$course'";
        $batches = mysqli_query($this->db,$sql);
        return $batches;
    }
    public function ensureAcademicSessionsSchema()
    {
        mysqli_query($this->db,
            "CREATE TABLE IF NOT EXISTS `academic_sessions` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `session_name` varchar(50) NOT NULL,
              `status` tinyint(1) NOT NULL DEFAULT 1,
              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
              PRIMARY KEY (`id`),
              UNIQUE KEY `session_name` (`session_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
        $col = mysqli_query($this->db, "SHOW COLUMNS FROM `stud_details` LIKE 'session_name'");
        if ($col && mysqli_num_rows($col) === 0) {
            mysqli_query($this->db, "ALTER TABLE `stud_details` ADD COLUMN `session_name` varchar(50) DEFAULT NULL AFTER `batch`");
        }
    }

    public function getAcademicSessions()
    {
        $this->ensureAcademicSessionsSchema();
        return mysqli_query($this->db, "SELECT * FROM `academic_sessions` WHERE `status` = 1 ORDER BY `session_name` DESC");
    }

    public function fetchstudentwithbatch($cou, $batch)
    {
        $cou = mysqli_real_escape_string($this->db, $cou);
        $batch = mysqli_real_escape_string($this->db, $batch);
        $sql = "SELECT * FROM `stud_details` WHERE `course_name`= '$cou' AND `batch`='$batch' ";
        return mysqli_query($this->db, $sql);
    }

    /** Filter students by course; session and batch optional. */
    public function fetchStudentsFiltered($cou, $session, $batch = '')
    {
        $cou = mysqli_real_escape_string($this->db, $cou);
        $sql = "SELECT * FROM `stud_details` WHERE `course_name` = '$cou'";
        if ($session !== null && $session !== '') {
            $session = mysqli_real_escape_string($this->db, $session);
            $sql .= " AND `session_name` = '$session'";
        }
        if ($batch !== null && $batch !== '') {
            $batch = mysqli_real_escape_string($this->db, $batch);
            $sql .= " AND `batch` = '$batch'";
        }
        $sql .= ' ORDER BY `name` ASC';
        return mysqli_query($this->db, $sql);
    }

    public function getusers(){
        $sql = "SELECT * FROM `users`";
        $user = mysqli_query($this->db,$sql);
        return $user;
    }

    public function fetchAttendance($student_id){
        $student_id = (int) $student_id;
        $sql = "SELECT 
                sid,
                course,
                batch,
                COUNT(DISTINCT date) AS total_days, 
                SUM(CASE WHEN LOWER(status) = 'present' THEN 1 ELSE 0 END) AS present_days, 
                SUM(CASE WHEN LOWER(status) = 'absent' THEN 1 ELSE 0 END) AS absent_days, 
                (SUM(CASE WHEN LOWER(status) = 'present' THEN 1 ELSE 0 END) / NULLIF(COUNT(DISTINCT date), 0)) * 100 AS attendance_percentage
              FROM attendance
              WHERE sid = '$student_id' 
              GROUP BY sid, course, batch";
        return mysqli_query($this->db, $sql);
    }

    public function ensureTeacherAttendanceSchema()
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        mysqli_query($this->db,
            "CREATE TABLE IF NOT EXISTS `teacher_attendance` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `tid` int(11) NOT NULL,
              `date` date NOT NULL,
              `entry_time` datetime DEFAULT NULL,
              `exit_time` datetime DEFAULT NULL,
              `course` varchar(255) DEFAULT NULL,
              `status` varchar(50) DEFAULT '',
              PRIMARY KEY (`id`),
              KEY `idx_tid_date` (`tid`, `date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }

    /** Summary by course for logged-in teacher (NFC card in/out). */
    public function fetchMyTeacherAttendance($teacherId)
    {
        $this->ensureTeacherAttendanceSchema();
        $teacherId = (int) $teacherId;
        $sql = "SELECT
                tid,
                course,
                COUNT(DISTINCT date) AS total_days,
                SUM(CASE WHEN LOWER(status) = 'present' THEN 1 ELSE 0 END) AS present_days,
                SUM(CASE WHEN LOWER(status) = 'absent' THEN 1 ELSE 0 END) AS absent_days,
                (SUM(CASE WHEN LOWER(status) = 'present' THEN 1 ELSE 0 END) / NULLIF(COUNT(DISTINCT date), 0)) * 100 AS attendance_percentage
              FROM teacher_attendance
              WHERE tid = '$teacherId'
              GROUP BY tid, course
              ORDER BY course ASC";
        return mysqli_query($this->db, $sql);
    }

    /** Daily in/out log for logged-in teacher. */
    public function fetchMyTeacherAttendanceLog($teacherId)
    {
        $this->ensureTeacherAttendanceSchema();
        $teacherId = (int) $teacherId;
        $sql = "SELECT date, course, entry_time, exit_time, status
                FROM teacher_attendance
                WHERE tid = '$teacherId'
                ORDER BY date DESC, entry_time DESC";
        return mysqli_query($this->db, $sql);
    }
    
    
     public function getBatchByCourse($course)
        {
            $sql = "SELECT *
            FROM batches
            WHERE course='$course'
            AND status='1'
            ORDER BY name";

    return mysqli_query($this->db,$sql);
        }
        public function fetchAllSessions()
        {
            $sql = "SELECT *
                    FROM academic_sessions
                    WHERE status='1'
                    ORDER BY id DESC";
        
            return mysqli_query($this->db,$sql);
        }
        public function fetchStudentFilter($course,$batch,$session)
        {
            $sql = "SELECT *
                    FROM stud_details
                    WHERE course_name='$course'
                    AND batch='$batch'
                    AND session_name='$session'
                    ORDER BY id DESC";
        
            return mysqli_query($this->db,$sql);
        }
        

    
}



?>