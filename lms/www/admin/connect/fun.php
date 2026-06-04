<?php

class fun
{
    private $db;
    function __construct($con)
    {
        $this->db = $con;

    }
    
    
    
    //mark absent
    
    
    public function mark_all_absentees()
{
    $date = date('Y-m-d');
    $countTotal = 0;

    // Get all distinct course_name and batch combinations
    $groups = mysqli_query($this->db, "SELECT DISTINCT course_name, batch FROM students");
    if (!$groups) {
        return "Error fetching course_name & batch groups: " . mysqli_error($this->db);
    }

    while ($group = mysqli_fetch_assoc($groups)) {
        $course_name = $group['course_name'];
        $batch = $group['batch'];

        // Get students in this course_name & batch
        $students = mysqli_query($this->db, "SELECT id FROM students WHERE course_name = '$course_name' AND batch = '$batch'");
        if (!$students) {
            return "Error fetching students: " . mysqli_error($this->db);
        }

        while ($student = mysqli_fetch_assoc($students)) {
            $sid = $student['id'];

            // Check if attendance exists for this student, course_name, batch and date
            $check = mysqli_query($this->db, "SELECT id FROM attendance WHERE sid = '$sid' AND course = '$course_name' AND batch = '$batch' AND date = '$date'");
            if (!$check) {
                return "Error checking attendance: " . mysqli_error($this->db);
            }

            if (mysqli_num_rows($check) == 0) {
                // Mark absent
                $insert = mysqli_query($this->db, "INSERT INTO attendance (sid, course, batch, date, status) VALUES ('$sid', '$course_name', '$batch', '$date', 'Absent')");
                if (!$insert) {
                    return "Error inserting attendance: " . mysqli_error($this->db);
                }
                $countTotal++;
            }
        }
    }

    return "Marked $countTotal absentees for all courses and batches on $date.";
}




    public function login($username,$password){
        
        $query    = "SELECT * FROM `admin` WHERE `username`='$username' AND `password` = '$password'";
        $result = mysqli_query($this->db, $query);

        
        $rows = mysqli_num_rows($result);
        if ($rows == 1) {
           
            
                return ["Done",1];
            
           
            // Redirect to user dashboard page
           
             
        }
        else{
            return ["Failed",0];
        }
    }

    // public function getUnassignedCourses($email) {
    //     $query = "SELECT * FROM courses 
    //               WHERE course_name NOT IN (
    //                   SELECT course_name FROM assigned_courses WHERE teacher_email = '$email'
    //               )";
    //     return $query;
    // }


    public function getStudentByUID($uid)
    {
        $uid = mysqli_real_escape_string($this->db, $uid);
        $sql = "SELECT * FROM `stud_details` WHERE id = '$uid'";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }
      public function fetchstudentpass()
    {
        $sql = "SELECT users.sid, users.pass, stud_details.name AS student_name 
            FROM users 
            JOIN stud_details ON users.sid = stud_details.id";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }
       public function fetchparentpass()
    {
        $sql = "SELECT parent.id, parent.pass, stud_details.name AS student_name 
            FROM parent 
            JOIN stud_details ON parent.id = stud_details.id;";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }
    
        public function edit_stud_pass($id,$pass)
    {
        $sql = "UPDATE `users` SET `pass`='$pass' WHERE `sid`='$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    
         public function edit_parent_pass($id,$pass)
    {
        $sql = "UPDATE `parent` SET `pass`='$pass' WHERE `id`='$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    
    
    public function updatecid($studentId,$newFile)
    {
        $sql = "UPDATE `stud_details` SET `CID` = '1' , `pfp` = '$newFile' WHERE `stud_details`.`id` = '$studentId';";
        $query = mysqli_query($this->db, $sql);
        return $query;
    } 
    public function getStudentBy_UID($uid)
    {
        $uid = mysqli_real_escape_string($this->db, $uid);
        $sql = "SELECT * FROM `stud_details` WHERE `uid` = '$uid'";
        $result = mysqli_query($this->db, $sql);
        return $result;
    }

    // public function markAttendance($studentId , $batch,$course)
    // {
    //     $studentId = (int)$studentId; // force integer
    //     $today = date('Y-m-d');
    //     $sql = "INSERT INTO `attendance`(`sid`,`date`, `course`, `batch`, `status`) VALUES ('$studentId','$today','$course','$batch','Present')";
    //     $result = mysqli_query($this->db, $sql);
    //     return $result;
    // }


//     public function markAttendance($studentId, $courseName, $batchName)
    
// {
//     date_default_timezone_set('Asia/Kolkata');
//     $studentId = (int)$studentId;
//     $now = date('Y-m-d H:i:s');
//     $today = date('Y-m-d');

//     // 1. Check if today's record exists
//     $sql = "SELECT * FROM attendance 
//             WHERE sid = '$studentId' 
//             AND date = '$today'";
//     $result = mysqli_query($this->db, $sql);

//     if (mysqli_num_rows($result) > 0) {
//         $record = mysqli_fetch_assoc($result);

//         // Already marked both entry and exit
//         if (!empty($record['exit_time'])) {
//             return [
//                 "status" => "already_completed",
//                 "entry" => $record['entry_time'],
//                 "exit" => $record['exit_time']
//             ];
//         }

//         // Entry exists, exit missing
//         $entryTime = strtotime($record['entry_time']);
//         $nowTime = strtotime($now);
//         $timeDiff = $nowTime - $entryTime;

//         if ($timeDiff >= 300) {
//             // Mark exit and set status = Present
//             $update = "UPDATE attendance 
//                        SET exit_time = '$now', status = 'Present' 
//                        WHERE sid = '$studentId' AND date = '$today'";
//             if (mysqli_query($this->db, $update)) {
//                 return [
//                     "status" => "exit_marked",
//                     "exit" => $now
//                 ];
//             } else {
//                 return ["status" => "error"];
//             }
//         } else {
//             $minutesLeft = ceil((300 - $timeDiff) / 60);
//             return [
//                 "status" => "wait",
//                 "minutes_left" => $minutesLeft
//             ];
//         }

//     } else {
//         // No record yet — mark entry
//         $insert = "INSERT INTO attendance 
//                    (sid, date, entry_time, course, batch, status)
//                    VALUES ('$studentId', '$today', '$now', '$courseName', '$batchName', '')";
//         if (mysqli_query($this->db, $insert)) {
//             return [
//                 "status" => "entry_marked",
//                 "entry" => $now
//             ];
//         } else {
//             return ["status" => "error"];
//         }
//     }
// }



public function markAttendance($studentId, $courseName, $batchName)
{
    date_default_timezone_set('Asia/Kolkata');
    $studentId = (int)$studentId;
    $now = date('Y-m-d H:i:s');
    $today = date('Y-m-d');

    // 1. Check if today's record exists (check for entry and exit)
    $sql = "SELECT * FROM attendance 
            WHERE sid = '$studentId' 
            AND date = '$today'";
    $result = mysqli_query($this->db, $sql);

    if (mysqli_num_rows($result) > 0) {
        $record = mysqli_fetch_assoc($result);

        // Already marked both entry and exit
        if (!empty($record['exit_time'])) {
            return [
                "status" => "already_completed",
                "entry" => $record['entry_time'],
                "exit" => $record['exit_time']
            ];
        }

        // Entry exists, exit missing
        $entryTime = strtotime($record['entry_time']);
        $nowTime = strtotime($now);
        $timeDiff = $nowTime - $entryTime;

        if ($timeDiff >= 3600) {
            // Mark exit and set status = Present
            $update = "UPDATE attendance 
                       SET exit_time = '$now', status = 'Present' 
                       WHERE sid = '$studentId' AND date = '$today' AND exit_time IS NULL";
            if (mysqli_query($this->db, $update)) {
                return [
                    "status" => "exit_marked",
                    "exit" => $now
                ];
            } else {
                return ["status" => "error"];
            }
        } else {
            // Entry exists, but the exit is not yet possible
            $minutesLeft = ceil((3600 - $timeDiff) / 60);
            return [
                "status" => "wait",
                "minutes_left" => $minutesLeft
            ];
        }

    } else {
        // No record exists for today — mark entry
        $insert = "INSERT INTO attendance 
                   (sid, date, entry_time, course, batch, status)
                   VALUES ('$studentId', '$today', '$now', '$courseName', '$batchName', '')";
        if (mysqli_query($this->db, $insert)) {
            return [
                "status" => "entry_marked",
                "entry" => $now
            ];
        } else {
            return ["status" => "error"];
        }
    }
}

public function mark_absentees() 
{
    $date = date('Y-m-d');
    $count = 0;

    $students = mysqli_query($this->db, "SELECT id FROM students");

    while ($student = mysqli_fetch_assoc($students)) {
        $student_id = $student['id'];
        $check = mysqli_query($this->db, "SELECT id FROM attendance WHERE student_id = $student_id AND date = '$date'");

        if (mysqli_num_rows($check) == 0) {
            mysqli_query($this->db, "INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', 'Absent')");
            $count++;
        }
    }

    return "Marked $count students as absent for $date.";
}


public function fetchAllStudentscid()
{
    $sql = "SELECT * FROM `stud_details` WHERE (`CID` IS NULL OR `CID` = '' OR `CID` = '0') ORDER BY `id` ASC";
    return mysqli_query($this->db, $sql);
}

/** All stud_details columns for students without CID (card / photo not completed). */
public function fetchStudentsWithoutCidForExport()
{
    return $this->fetchAllStudentscid();
}

public function countStudentsWithoutCid()
{
    $res = mysqli_query($this->db,
        "SELECT COUNT(*) AS c FROM `stud_details` WHERE (`CID` IS NULL OR `CID` = '' OR `CID` = '0')"
    );
    if (!$res) {
        return 0;
    }
    $row = mysqli_fetch_assoc($res);
    return (int) ($row['c'] ?? 0);
}

/** Students without CID, optionally limited to numeric ids. */
public function fetchStudentsWithoutCidByIds(array $ids = [])
{
    $where = "(`CID` IS NULL OR `CID` = '' OR `CID` = '0')";
    $ids = array_values(array_filter(array_map('intval', $ids), static function ($id) {
        return $id > 0;
    }));
    if ($ids) {
        $where .= ' AND `id` IN (' . implode(',', $ids) . ')';
    }
    $sql = "SELECT * FROM `stud_details` WHERE $where ORDER BY `id` ASC";
    return mysqli_query($this->db, $sql);
}

    
    public function deletesalaryhistory($id)
    {
        $sql = "DELETE FROM `teacher_salary` WHERE `srno` = '$id'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function checkassignedcourses($email,$course){
        $sql = "SELECT * FROM `course_assign` WHERE `email` = '$email' AND `course` = '$course'";
        $fetch = mysqli_query($this->db, $sql);
        if(mysqli_num_rows($fetch) == 0){
             return true;
        }
        else{
             return false;
        }
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
       public function get_today_attendance($course){
         $today = date('Y-m-d');
        $sql = "SELECT * FROM attendance WHERE course = '$course' AND date = '$today'";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
   }
   
   public function getStudentNameById($student_id) {
    $sql = "SELECT name FROM stud_details WHERE id = '$student_id'";
    $result = mysqli_query($this->db, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['name'];
    }
    return "Unknown";
}

       public function addanothercourse($email,$course){
        $sql = "INSERT INTO `course_assign`(  `email`,`course`) VALUES ('$email','$course')";
        $cos = mysqli_query($this->db, $sql);
        
             return $cos;
       }
       public function teacherpaysalary($tid, $name,$email, $course, $salary, $formatted_date){
        $sql = "INSERT INTO `teacher_salary`( `tid`, `name`,`email`, `course`, `salary`,`date`) VALUES ('$tid','$name','$email','$course','$salary','$formatted_date')";
        $fetch = mysqli_query($this->db, $sql);
        
             return $fetch;
       }

       public function fetchteachersalary()
       {
           $sql = "SELECT * FROM `teacher_salary`";
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

    // public function insertStudentDetails($POST, $FILE)
    // {
    //     $target_dir = "student_pfp/";
    //     $pfp = $target_dir . basename($FILE["pfp"]["name"]);
    //     $pfpType = strtolower(pathinfo($pfp, PATHINFO_EXTENSION));
    //     $pfpname = $POST['name'] . "_PFP" . ".$pfpType";
    //     $pfp = $target_dir . $pfpname;
    //     //echo "$pfpname";
    //     $name = $POST['name'];
    //     $age = $POST['age'];
    //     $email = $POST['email'];
    //     $city = $POST['city'];
    //     $state = $POST['state'];
    //     $mobile = $POST['mobile'];
    //     $school = $POST['school'];
    //     $course = $POST['course'];
    //     $batch = $POST['batch'];
    //     $c = $this->fetchCourseWithname($course);
    //     $co = mysqli_fetch_assoc($c);

    //     $fees = $co['course_fees'];
    //     $aadhar = $POST['aadhar'];
    //     $dob = $POST['dob'];
    //     $address = $POST['address'];
        


    //     $sql = "INSERT INTO `stud_details`(`name`,`pfp`,`age`, `mobile`, `school_name`, `email`, `city`, `state`, `course_name`, `course_fees`,`balance_fees`,`batch`, `aadhar`,`dob`,`address`,`date_of_joining`) 
    //     VALUES ('".$name."','".$pfpname."','".$age ."','".$mobile."','".$school."','$email' ,'$city','$state','". $course."','" . $fees . "','" . $fees . "',' $batch','" . $aadhar . "','" . $dob . "','" . $address . "',curdate())";
    //     $query = mysqli_query($this->db, $sql);
    //     if ($query) {
    //         move_uploaded_file($FILE["pfp"]["tmp_name"], $pfp);

    //     }
    //     return $query;
    // }
    
    
    public function insertStudentDetails($POST, $FILE)
{
    $target_dir = "student_pfp/";
    $pfp = $target_dir . basename($FILE["pfp"]["name"]);
    $pfpType = strtolower(pathinfo($pfp, PATHINFO_EXTENSION));
    $pfpname = $POST['name'] . "_PFP" . ".$pfpType";
    $pfp = $target_dir . $pfpname;

    $name = $POST['name'];
    $age = $POST['age'];
    $email = $POST['email'];
    $city = $POST['city'];
    $state = $POST['state'];
    $mobile = $POST['mobile'];
    $school = $POST['school'];
    $course = $POST['course'];
    $batch = $POST['batch'];
    $session = mysqli_real_escape_string($this->db, $POST['session'] ?? '');
    $c = $this->fetchCourseWithname($course);
    $co = mysqli_fetch_assoc($c);

    $fees = $co['course_fees'];
    $aadhar = $POST['aadhar'];
    $dob = $POST['dob'];
    $address = $POST['address'];

    $sql = "INSERT INTO `stud_details`(`name`,`pfp`,`age`, `mobile`, `school_name`, `email`, `city`, `state`, `course_name`, `course_fees`,`balance_fees`,`batch`, `session_name`, `aadhar`,`dob`,`address`,`date_of_joining`) 
            VALUES ('".$name."','".$pfpname."','".$age ."','".$mobile."','".$school."','$email' ,'$city','$state','". $course."','" . $fees . "','" . $fees . "','".$batch."','".$session."','" . $aadhar . "','" . $dob . "','" . $address . "',curdate())";

    $query = mysqli_query($this->db, $sql);
    if ($query) {
        move_uploaded_file($FILE["pfp"]["tmp_name"], $pfp);

        $id = mysqli_insert_id($this->db); // ✅ Reliable and direct

        // Insert into parent and users
        mysqli_query($this->db, "INSERT INTO `parent`(`id`) VALUES ('$id')");
        mysqli_query($this->db, "INSERT INTO `users`(`sid`) VALUES ('$id')");

        return $query; // ✅ Return the ID directly
    }

    return false;
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
        $sessionRaw = trim((string) ($POST['session'] ?? ''));
        $sessionSql = $sessionRaw === ''
            ? 'NULL'
            : "'" . mysqli_real_escape_string($this->db, $sessionRaw) . "'";
        $school = $POST['school'];
        $cid = $POST['course'];
        $c = $this->fetchCourseWithId($cid);
        $co = mysqli_fetch_assoc($c);
        $course = $co['course_name'];
        $fees = $POST['fees'];
        $aadhar = $POST['aadhar'];
        $email = $POST['email'];
        $address = $POST['address'];
        $dob = $POST['dob'];
        $date = ($POST['date']!=null)?($POST['date']):(date("d-m-Y"));
        $balance = $fees - $PaidFees;

        
        $sql = "UPDATE `stud_details` SET `name`='" . $name . "',`age`='" . $age . "',`mobile`='" . $mobile . "',`school_name`='" . $school . "',`email`='" . $email . "',`address`='" . $address . "',`dob`='" . $dob . "',`course_name`='" . $course . "',`course_fees`='" . $fees . "',`balance_fees`='" . $balance . "',`aadhar`='" . $aadhar . "',`date_of_joining`='" . $date . "',`batch` ='" . $batch . "',`session_name`=" . $sessionSql . " WHERE `id`='" . $id . "'";
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

    $sql = "INSERT INTO `registered_stud`(`name`,`pfp`,`age`, `mobile`, `school_name`, `email`, `city`, `state`, `course_name`, `course_fees`,`balance_fees`,`batch`, `aadhar`,`date_of_joining`) 
            VALUES ('" . $name . "','" . $pfpname . "','" . $age . "','" . $mobile . "','" . $school . "','$email' ,'$city','$state','" . $course . "','" . $fees . "','" . $fees . "','$batch','" . $aadhar . "',curdate())";

    $query = mysqli_query($this->db, $sql);
    if ($query) {
        move_uploaded_file($FILE["pfp"]["tmp_name"], $pfp);

        // Add header location for redirection after successful query
        header("Location: ../index.php"); // Change 'success.php' to your desired redirection page
        exit(); // Stop further script execution after redirection
    } else {
        // Optional: Handle query failure (logging, error page, etc.)
        echo "Error: " . mysqli_error($this->db);
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
    public function deletetesubject($id)
    {
        $sql = "DELETE FROM `subject` WHERE `id` = $id";
        $delete = mysqli_query($this->db, $sql);
        return $delete;
    }
    public function getSubjectDetails()
    {
        $sql = "SELECT * FROM `subject` ";
        $query = mysqli_query($this->db, $sql);
        return $query;
    }
    public function getSubjectDetailsWithCourse($course)
    {
        $sql = "SELECT * FROM `subject` WHERE `course_name`='$course' ";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
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

    public function updateWorkingStatus($status = 0, $id)
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
    public function fetchAllCourses(){
        $sql = "SELECT * FROM `course_details` ORDER BY `id` DESC";
        $fetch = mysqli_query($this->db, $sql);
         return $fetch;
       }
    public function fetchRegStudentsWithId($id)
    {
        $sql = "SELECT * FROM `registered_stud` WHERE `id` = $id";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    // public function fetchCourseWithname($name)
    // {
    //     $sql = "SELECT * FROM course_details WHERE course_name LIKE '%$name%'";
    //     $fetch = mysqli_query($this->db, $sql);
    //     return $fetch;
    // }
    public function transferRegStud($id)
    {
        
        
        $regstud = $this->fetchRegStudentsWithId($id);
        $reg = mysqli_fetch_assoc($regstud);
        //$course = $this->fetchCourseWithId($reg['course_name']);
        $course = $this->fetchCourseWithname($reg['course_name']);
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
        $fetch = "SELECT * FROM `batches` ORDER BY `id` DESC";
        $result = mysqli_query($this->db, $fetch);
        return $result;
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
        $fetch = "SELECT * FROM `academic_sessions` WHERE `status` = 1 ORDER BY `session_name` DESC";
        return mysqli_query($this->db, $fetch);
    }

    public function getAllAcademicSessions()
    {
        $this->ensureAcademicSessionsSchema();
        $fetch = "SELECT * FROM `academic_sessions` ORDER BY `session_name` DESC";
        return mysqli_query($this->db, $fetch);
    }

    public function insertAcademicSession($sessionName)
    {
        $this->ensureAcademicSessionsSchema();
        $sessionName = mysqli_real_escape_string($this->db, trim($sessionName));
        if ($sessionName === '') {
            return false;
        }
        $sql = "INSERT INTO `academic_sessions` (`session_name`, `status`) VALUES ('$sessionName', 1)";
        return mysqli_query($this->db, $sql);
    }

    public function getAcademicSessionById($id)
    {
        $this->ensureAcademicSessionsSchema();
        $id = (int) $id;
        if ($id <= 0) {
            return false;
        }
        $sql = "SELECT * FROM `academic_sessions` WHERE `id` = '$id' LIMIT 1";
        return mysqli_query($this->db, $sql);
    }

    public function updateAcademicSession($id, $sessionName)
    {
        $this->ensureAcademicSessionsSchema();
        $id = (int) $id;
        $sessionName = mysqli_real_escape_string($this->db, trim((string) $sessionName));
        if ($id <= 0 || $sessionName === '') {
            return false;
        }
        $curRes = $this->getAcademicSessionById($id);
        $cur = $curRes ? mysqli_fetch_assoc($curRes) : null;
        if (!$cur) {
            return false;
        }
        $oldName = mysqli_real_escape_string($this->db, $cur['session_name']);
        $sql = "UPDATE `academic_sessions` SET `session_name` = '$sessionName' WHERE `id` = '$id'";
        if (!mysqli_query($this->db, $sql)) {
            return false;
        }
        if ($oldName !== $sessionName) {
            mysqli_query(
                $this->db,
                "UPDATE `stud_details` SET `session_name` = '$sessionName' WHERE `session_name` = '$oldName'"
            );
        }
        return true;
    }

    public function updateSessionStatus($id, $currentStatus)
    {
        $id = (int) $id;
        $newStatus = ((int) $currentStatus === 1) ? 0 : 1;
        $sql = "UPDATE `academic_sessions` SET `status` = '$newStatus' WHERE `id` = '$id'";
        return mysqli_query($this->db, $sql);
    }

    public function countStudentsInAcademicSession($sessionName)
    {
        $sessionName = mysqli_real_escape_string($this->db, trim((string) $sessionName));
        if ($sessionName === '') {
            return 0;
        }
        $res = mysqli_query(
            $this->db,
            "SELECT COUNT(*) AS c FROM `stud_details` WHERE `session_name` = '$sessionName'"
        );
        if (!$res) {
            return 0;
        }
        $row = mysqli_fetch_assoc($res);
        return (int) ($row['c'] ?? 0);
    }

    public function deleteAcademicSession($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return ['ok' => false, 'message' => 'Invalid session'];
        }
        $res = $this->getAcademicSessionById($id);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if (!$row) {
            return ['ok' => false, 'message' => 'Session not found'];
        }
        $inUse = $this->countStudentsInAcademicSession($row['session_name']);
        if ($inUse > 0) {
            return [
                'ok' => false,
                'message' => 'Cannot delete: ' . $inUse . ' student(s) are assigned to this session',
            ];
        }
        $sql = "DELETE FROM `academic_sessions` WHERE `id` = '$id'";
        if (mysqli_query($this->db, $sql) && mysqli_affected_rows($this->db) > 0) {
            return ['ok' => true, 'message' => 'Deleted'];
        }
        return ['ok' => false, 'message' => 'Could not delete session'];
    }

    public function ensurePreviousSessionArchiveSchema()
    {
        mysqli_query($this->db,
            "CREATE TABLE IF NOT EXISTS `stud_details_previous_session` (
              `archive_id` int(11) NOT NULL AUTO_INCREMENT,
              `original_student_id` int(11) NOT NULL,
              `session_name` varchar(50) NOT NULL,
              `name` varchar(255) NOT NULL,
              `pfp` text DEFAULT NULL,
              `age` int(11) NOT NULL DEFAULT 0,
              `mobile` varchar(15) NOT NULL DEFAULT '',
              `school_name` varchar(255) NOT NULL DEFAULT '',
              `email` varchar(255) NOT NULL DEFAULT '',
              `city` varchar(255) NOT NULL DEFAULT '',
              `state` varchar(255) NOT NULL DEFAULT '',
              `course_name` varchar(255) NOT NULL DEFAULT '',
              `course_fees` int(11) NOT NULL DEFAULT 0,
              `balance_fees` int(11) NOT NULL DEFAULT 0,
              `aadhar` varchar(12) NOT NULL DEFAULT '0',
              `dob` date DEFAULT NULL,
              `address` text DEFAULT NULL,
              `date_of_joining` date DEFAULT NULL,
              `batch` varchar(255) NOT NULL DEFAULT 'none',
              `status` tinyint(1) NOT NULL DEFAULT 0,
              `CID` varchar(20) DEFAULT NULL,
              `uid` varchar(50) DEFAULT NULL,
              `transferred_at` timestamp NOT NULL DEFAULT current_timestamp(),
              `transferred_by` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`archive_id`),
              KEY `idx_session_name` (`session_name`),
              KEY `idx_original_student_id` (`original_student_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }

    /** WHERE clause fragment for active students in a session key (__none__ = no session set). */
    private function sessionWhereClause($sessionKey)
    {
        if ($sessionKey === '__none__') {
            return "(session_name IS NULL OR session_name = '')";
        }
        $sessionKey = mysqli_real_escape_string($this->db, $sessionKey);
        return "session_name = '$sessionKey'";
    }

    private function sessionArchiveLabel($sessionKey)
    {
        return $sessionKey === '__none__' ? '(No session)' : $sessionKey;
    }

    public function getSessionTransferOptions()
    {
        $this->ensurePreviousSessionArchiveSchema();
        $options = [];
        $sql = "SELECT session_name, COUNT(*) AS cnt FROM stud_details
                WHERE session_name IS NOT NULL AND session_name != ''
                GROUP BY session_name ORDER BY session_name DESC";
        $res = mysqli_query($this->db, $sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $options[] = [
                    'key' => $row['session_name'],
                    'label' => $row['session_name'],
                    'count' => (int) $row['cnt'],
                ];
            }
        }
        $noneRes = mysqli_query($this->db,
            "SELECT COUNT(*) AS cnt FROM stud_details WHERE session_name IS NULL OR session_name = ''"
        );
        if ($noneRes) {
            $none = mysqli_fetch_assoc($noneRes);
            $noneCount = (int) ($none['cnt'] ?? 0);
            if ($noneCount > 0) {
                $options[] = [
                    'key' => '__none__',
                    'label' => '(No session assigned)',
                    'count' => $noneCount,
                ];
            }
        }
        return $options;
    }

    public function countActiveStudentsForSessionKey($sessionKey)
    {
        $where = $this->sessionWhereClause($sessionKey);
        $res = mysqli_query($this->db, "SELECT COUNT(*) AS c FROM stud_details WHERE $where");
        if (!$res) {
            return 0;
        }
        $row = mysqli_fetch_assoc($res);
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Copy students to stud_details_previous_session and remove from active tables.
     * @return array{ok:bool, count?:int, error?:string}
     */
    public function transferSessionStudentsToArchive($sessionKey, $transferredBy)
    {
        $this->ensurePreviousSessionArchiveSchema();
        $where = $this->sessionWhereClause($sessionKey);
        $label = mysqli_real_escape_string($this->db, $this->sessionArchiveLabel($sessionKey));
        $by = mysqli_real_escape_string($this->db, $transferredBy);

        $idRes = mysqli_query($this->db, "SELECT id FROM stud_details WHERE $where");
        if (!$idRes) {
            return ['ok' => false, 'error' => mysqli_error($this->db)];
        }
        $ids = [];
        while ($row = mysqli_fetch_assoc($idRes)) {
            $ids[] = (int) $row['id'];
        }
        if (count($ids) === 0) {
            return ['ok' => false, 'error' => 'No active students found for this session.'];
        }

        mysqli_begin_transaction($this->db);

        $insertSql = "INSERT INTO stud_details_previous_session (
            original_student_id, session_name, name, pfp, age, mobile, school_name, email, city, state,
            course_name, course_fees, balance_fees, aadhar, dob, address, date_of_joining, batch, status, CID, uid,
            transferred_by
        )
        SELECT id, '$label', name, IFNULL(pfp,''), age, mobile, school_name, email, city, state,
            course_name, course_fees, balance_fees, aadhar, dob, address, date_of_joining, batch, status, CID, uid,
            '$by'
        FROM stud_details WHERE $where";

        if (!mysqli_query($this->db, $insertSql)) {
            mysqli_rollback($this->db);
            return ['ok' => false, 'error' => 'Archive copy failed: ' . mysqli_error($this->db)];
        }

        $idList = implode(',', $ids);
        mysqli_query($this->db, "DELETE FROM users WHERE sid IN ($idList)");
        mysqli_query($this->db, "DELETE FROM parent WHERE id IN ($idList)");

        if (!mysqli_query($this->db, "DELETE FROM stud_details WHERE id IN ($idList)")) {
            mysqli_rollback($this->db);
            return ['ok' => false, 'error' => 'Could not remove active students: ' . mysqli_error($this->db)];
        }

        mysqli_commit($this->db);
        return ['ok' => true, 'count' => count($ids)];
    }

    public function getArchivedSessionStudents($sessionFilter = '')
    {
        $this->ensurePreviousSessionArchiveSchema();
        $sql = "SELECT * FROM stud_details_previous_session";
        if ($sessionFilter !== '') {
            $sessionFilter = mysqli_real_escape_string($this->db, $sessionFilter);
            $sql .= " WHERE session_name = '$sessionFilter'";
        }
        $sql .= " ORDER BY transferred_at DESC, name ASC";
        return mysqli_query($this->db, $sql);
    }

    public function getArchivedSessionNames()
    {
        $this->ensurePreviousSessionArchiveSchema();
        return mysqli_query($this->db,
            "SELECT DISTINCT session_name FROM stud_details_previous_session ORDER BY session_name DESC"
        );
    }

    public function countArchivedStudents($sessionFilter = '')
    {
        $this->ensurePreviousSessionArchiveSchema();
        $sql = "SELECT COUNT(*) AS c FROM stud_details_previous_session";
        if ($sessionFilter !== '') {
            $sessionFilter = mysqli_real_escape_string($this->db, $sessionFilter);
            $sql .= " WHERE session_name = '$sessionFilter'";
        }
        $res = mysqli_query($this->db, $sql);
        if (!$res) {
            return 0;
        }
        $row = mysqli_fetch_assoc($res);
        return (int) ($row['c'] ?? 0);
    }

    public function getBatchesByCourse($courseName)
    {
        $courseName = mysqli_real_escape_string($this->db, $courseName);
        $fetch = "SELECT * FROM `batches` WHERE `course` = '$courseName' AND `status` = 1 ORDER BY `name` ASC";
        return mysqli_query($this->db, $fetch);
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
    public function fetchteachersalarybyemail($email)
    {
        $sql = "SELECT * FROM `teacher_salary` WHERE `email` = '$email' ";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
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
    public function fetchStudent($course){
        $sql = "SELECT * FROM `stud_details` WHERE `course_name`like'$course'";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
   }
   
   public function fetchsliderone(){
    $sql = "SELECT * FROM `slider_one` ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}
public function deletesliderone($id){
    $sql = "DELETE FROM `slider_one`WHERE `img_id`='$id' ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}
public function fetchslidertwo(){
    $sql = "SELECT * FROM `slider_two` ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}
public function deleteslidertwo($id){
    $sql = "DELETE FROM `slider_two`WHERE `img_id`='$id' ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}



public function fetchgallery(){
    $sql = "SELECT * FROM `tbl_photos` ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}
public function deletegallery($id){
    $sql = "DELETE FROM `tbl_photos`WHERE `img_id`='$id' ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}




public function fetchteacherupload(){
    $sql = "SELECT * FROM `teacher_photo` ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}
public function deleteteacherupload($id){
    $sql = "DELETE FROM `teacher_photo`WHERE `id`='$id' ";
    $fetch = mysqli_query($this->db,$sql);
    return $fetch;
}



   public function fetchnobatchStudent(){
    $sql = 'SELECT * FROM `stud_details` WHERE `batch`= "none"';
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
    public function fetchAttendance($student_id){
        $sql = "SELECT 
                sid,
                course,
                batch,
                COUNT(DISTINCT date) AS total_days, 
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_days, 
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent_days, 
                (SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(DISTINCT date)) * 100 AS attendance_percentage
              FROM attendance
              WHERE sid = '$student_id' 
              GROUP BY sid, course";
        $attend = mysqli_query($this->db,$sql);
        return $attend;
    }
    public function insertSubject($POST)
    {
        $course = $POST['course'];
        $name = $POST['name'];
       
        $sql = "INSERT INTO `subject`(`course_name`,`subject_name`) VALUES ('" . $course . "','" . $name . "')";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function fetchCourseWithemail($email){
        $sql = "SELECT * FROM `courses_subjects` WHERE `teacher_email` = '$email'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
       }
    //    public function checktable($course){
    //     $sql = "SELECT `course` FROM `timetable` WHERE `course` = '$course'";
    //     $courseee = mysqli_query($this->db, $sql);
    //     return $courseee;
    //    }
    //    public function insertTimetable($coursee,$tab){
    //     $sql = "INSERT INTO `timetable`(`course`,`timetable`) VALUES ('" . $coursee . "','" . $tab . "')";
    //     $timetable = mysqli_query($this->db, $sql);
    //     return $timetable;
    //    }

    public function insertTimetable($course, $tab) {
        // Check if the course exists in the timetable
        $sql_check = "SELECT `course` FROM `timetable` WHERE `course` = '$course'";
        $course_check = mysqli_query($this->db, $sql_check);
    
        if (mysqli_num_rows($course_check) > 0) {
            // If the course exists, update the timetable
            $sql_update = "UPDATE `timetable` SET `timetable` = '$tab' WHERE `course` = '$course'";
            $update_result = mysqli_query($this->db, $sql_update);
            return $update_result; // Return the result of the update query
        } else {
            // If the course doesn't exist, insert a new record
            $sql_insert = "INSERT INTO `timetable`(`course`, `timetable`) VALUES ('$course', '$tab')";
            $insert_result = mysqli_query($this->db, $sql_insert);
            return $insert_result; // Return the result of the insert query
        }
    }
    
       public function fetchTimetable($course){
        $sql = "SELECT * FROM `timetable` WHERE `course` = '$course'";
        $timetableResult = mysqli_query($this->db, $sql);
        return $timetableResult;
       }
       public function getCourseDetailsfromtimetable(){
        $sql = "SELECT `course` FROM `timetable`";
        $courseee = mysqli_query($this->db, $sql);
        return $courseee;
       }
}

?>