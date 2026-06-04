<?php
class fun
{
    private $db;
    function __construct($con)
    {
        $this->db = $con;

    }

    public function login($id,$password){
        
        $query    = "SELECT * FROM `users` WHERE `sid`='$id' AND `pass` = '$password'";
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

    public function fetchAllStudents()
    {
        $sql = "SELECT * FROM `stud_details` WHERE 1";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function fetchRegStudents()
    {
        $sql = "SELECT * FROM `registered_stud` WHERE 1";
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
        echo "$pfpname";
        $name = $POST['name'];
        $age = $POST['age'];
        $mobile = $POST['mobile'];
        $school = $POST['school'];
        $course = $POST['course'];
        $c = $this->fetchCourseWithId($course);
        $co = mysqli_fetch_assoc($c);

        $fees = $co['course_fees'];
        $aadhar = $POST['aadhar'];
        $date = date("d-m-Y");


        $sql = "INSERT INTO `registered_stud`(`name`,`pfp`,`age`, `mobile`, `school_name`, `course_name`, `course_fees`,`balance_fees`, `aadhar`,`date_of_joining`) VALUES ('" . $name . "','" . $pfpname . "','" . $age . "','" . $mobile . "','" . $school . "','" . $course . "','" . $fees . "','" . $fees . "','" . $aadhar . "','" . $date . "')";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            move_uploaded_file($FILE["pfp"]["tmp_name"], $pfp);

        }
        return $query;
    }

    public function insertStudentDetailMain($POST, $FILE)
    {
        $target_dir = "./admin/student_pfp/";
        $pfp = $target_dir . basename($FILE["pfp"]["name"]);
        $pfpType = strtolower(pathinfo($pfp, PATHINFO_EXTENSION));
        $pfpname = $POST['name'] . "_PFP" . ".$pfpType";
        $pfp = $target_dir . $pfpname;
        echo "$pfpname";
        $name = $POST['name'];
        $age = $POST['age'];
        $mobile = $POST['mobile'];
        $school = $POST['clg'];
        $course = $POST['course'];
        $c = $this->fetchCourseWithId($course);
        $co = mysqli_fetch_assoc($c);
        $course = $co['course_name'];
        $fees = $co['course_fees'];
        $aadhar = $POST['aadhar'];
        $date = date("d-m-Y");


        $sql = "INSERT INTO `registered_stud`(`name`,`pfp`,`age`, `mobile`, `school_name`, `course_name`, `course_fees`,`balance_fees`, `aadhar`,`date_of_joining`) VALUES ('" . $name . "','" . $pfpname . "','" . $age . "','" . $mobile . "','" . $school . "','" . $course . "','" . $fees . "','" . $fees . "','" . $aadhar . "','" . $date . "')";
        $query = mysqli_query($this->db, $sql);
        if ($query) {
            move_uploaded_file($FILE["pfp"]["tmp_name"], $pfp);

        }
        return $query;
    }

    public function updateStudent($POST,$FILES){
        $id = $POST['id'];
        $stud = $this->getStudentByID($id);
        $student = mysqli_fetch_assoc($stud);
        $name = $POST['name'];
        $age = $POST['age'];
        $mobile = $POST['mobile'];
        //edited



        $school = $POST['school'];
        
        $email = $POST['email'];
        $city = $POST['city'];
        $state = $POST['state'];
        $aadhar = $POST['aadhar'];
        if(isset($FILES['pfp']) && $FILES['pfp']['name'] != null){
            $target_dir = "../admin/student_pfp/";
            $pfp = $target_dir . basename($FILES["pfp"]["name"]);
            $pfpType = strtolower(pathinfo($pfp, PATHINFO_EXTENSION));
            $pfpname = $POST['name'] . "_PFP" . ".$pfpType";
            $pfp = $target_dir . $pfpname;
        }
        else{
            $pfpname = $student['pfp']; 
        }
       
        
        $sql = "UPDATE `stud_details` SET `name`='" . $name . "',`email`='$email',`city`='$city',`pfp`='$pfpname',`state`='$state',`age`='" . $age . "',`mobile`='" . $mobile . "',`school_name`='" . $school . "',`aadhar`='" . $aadhar . "' WHERE `id`='" . $id . "'";
        $fetchh = mysqli_query($this->db, $sql);
        if($fetchh && isset($FILES['pfp']) && $FILES['pfp']['name'] != null){
            move_uploaded_file($FILES["pfp"]["tmp_name"], $pfp);
        }
        return $fetchh;
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
        $sql = "SELECT * FROM `course_details` ";
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
    public function fetchCourseWithname($name)
    {
        $sql = "SELECT * FROM course_details WHERE course_name LIKE '%$name%'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function fetchCourseWithemail($email)
    {
        $sql = "SELECT * FROM `stud_details` WHERE `email`= '$email'";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function deleteCourse($id)
    {
        $sql = "DELETE FROM `course_details` WHERE `id` = $id";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }
    public function deleteassignment($aid)
    {
        $sql = "DELETE FROM `assignment` WHERE `id` = $aid";
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

    public function updateStudentDetail($POST)
    {
        $id = $POST['id'];
        $name = $POST['name'];
        $age = $POST['age'];
        $mobile = $POST['mobile'];
        $batch = $POST['batch'];
        $school = $POST['school'];
        $course = $POST['course'];
        $fees = $POST['fees'];
        $aadhar = $POST['aadhar'];
        $date = ($POST['date']!=null)?($POST['date']):(date("d-m-Y"));
        
        $sql = "UPDATE `stud_details` SET `name`='" . $name . "',`age`='" . $age . "',`mobile`='" . $mobile . "',`school_name`='" . $school . "',`course_name`='" . $course . "',`course_fees`='" . $fees . "',`aadhar`='" . $aadhar . "',`date_of_joining`='" . $date . "',`batch` ='" . $batch . "' WHERE `id`='" . $id . "'";
        $fetchh = mysqli_query($this->db, $sql);
        return $fetchh;
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

    // public function transferRegStud($id)
    // {

    //     $regstud = $this->fetchRegStudentsWithId($id);
    //     $reg = mysqli_fetch_assoc($regstud);
    //     $course = $this->fetchCourseWithId($reg['course_name']);
    //     $courses = mysqli_fetch_assoc($course);
    //     $dob = date("d-m-Y");
    //     $intern = "INSERT INTO `stud_details`(`id`, `name`, `pfp`, `age`, `mobile`, `school_name`, `course_name`, `course_fees`, `balance_fees`, `aadhar`, `date_of_joining`)
    //                  VALUES ('$id','".$reg['name']."','".$reg['pfp']."','".$reg['age']."','".$reg['mobile']."','".$reg['school_name']."','".$courses['course_name']."','".$courses['course_fees']."','".$courses['course_fees']."','".$reg['aadhar']."','$dob')";
    //     $query = mysqli_query($this->db, $intern);
    //     if ($query) {
    //         $this->deleteRegStud($id);
    //     }
    //     return $query;
    // }

    public function transferRegStud($id)
{
    // Fetch data from the registration table
    $regstud = $this->fetchRegStudentsWithId($id);
    $reg = mysqli_fetch_assoc($regstud);

    if (!$reg) {
        return false; // No data found
    }

    // Fetch course details
    $course = $this->fetchCourseWithId($reg['course_name']);
    //$course = $this->fetchCourseWithname($reg['course_name']);
    print_r($course);
    $courses = mysqli_fetch_assoc($course);

    if (!$courses) {
        return false; // No course found
    }

    // Prepare date in the correct format (assuming database uses 'Y-m-d')
    $dob = date("Y-m-d");

    // Prepare the SQL query using prepared statements
    $stmt = $this->db->prepare(
        "INSERT INTO `stud_details` (`id`, `name`, `pfp`, `age`, `mobile`, `school_name`, `course_name`, `course_fees`, `balance_fees`, `aadhar`, `date_of_joining`) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt === false) {
        die("Prepare failed: " . $this->db->error);
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param(
        "isssisssiss",
        $id,
        $reg['name'],
        $reg['pfp'],
        $reg['age'],
        $reg['mobile'],
        $reg['school_name'],
        $courses['course_name'],
        $courses['course_fees'],
        $courses['course_fees'], // Assuming no payment yet
        $reg['aadhar'],
        $dob
    );

    // Execute the query
    $query = $stmt->execute();

    if ($query) {
        // Delete the student from the registration table
        $this->deleteRegStud($id);
    } else {
        die("Execute failed: " . $stmt->error);
    }

    // Close the statement
    $stmt->close();

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
        $sql = "SELECT * FROM `stud_details` where `id` =" . $id . ";";
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

    public function deleteStudDetails($id)
    {
        $delete = "DELETE FROM `stud_details` WHERE id =" . $id . "";
        $result = mysqli_query($this->db, $delete);
        return $result;
    }

    public function filterStudentWithBatch($id, $batch)
    {
        $fetch = "SELECT * FROM stud_details where id =" . $id . " and `batch` = " . $batch . ";";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function getEarning()
    {
        $amount = "SELECT * FROM `earning` ORDER BY id DESC LIMIT 1 ";
        $am = mysqli_query($this->db, $amount);
        $earn = mysqli_fetch_assoc($am);
        return $earn['earning'];
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
        $name = $POST['name'];
        $start = $POST['start'];
        $end = $POST['end'];
        $sql = "INSERT INTO `batches`(`name`, `start_time`, `end_time`, `status`) VALUES ('" . $name . "','" . $start . "','" . $end . "','1')";
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
        $fetch = "SELECT * FROM batches ";
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

    public function insertTransactionHistory($id, $name, $remain, $reason)
    {
        $transaction = "INSERT INTO `transaction_history`( `user_id`, `name`, `amount`, `reason`) VALUES ('" . $id . "','" . $name . "','" . $remain . "','" . $reason . "')";
        mysqli_query($this->db, $transaction);
    }

    public function getTransactionWithId($id)
    {
        $fetch = "SELECT * FROM transaction_history where user_id ='" . $id . "';";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function fetchTransactionHistory()
    {
        $sql = "SELECT * FROM transaction_history  ORDER BY id DESC";
        $fetch = mysqli_query($this->db, $sql);
        return $fetch;
    }

    public function getTransactionWithLimit($start, $limit)
    {
        $fetch = "SELECT * FROM transaction_history ORDER BY id DESC  LIMIT $start, $limit";
        $result = mysqli_query($this->db, $fetch);
        return $result;
    }

    public function getLastEarningMonth()
    {
        $amount = "SELECT * FROM `earning` ORDER BY id DESC LIMIT 1 ";
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

    public function regStudent($POST){
        $id = $POST['sid'];
        $id = substr($id,3);
        $pass = $POST['password'];
        //$hash = password_hash($pass, PASSWORD_DEFAULT);
        $sql="INSERT INTO `users`( `sid`, `pass`) VALUES ('$id','$pass')";
        $fetch = mysqli_query($this->db,$sql);
        return $fetch;
    }


    public function fetchCoursesTaken($sid){
        $sql = "SELECT * FROM courses_taken WHERE `sid` = '$sid'";
        $fetch = mysqli_query($this->db,$sql);
        $fetch = mysqli_fetch_assoc($fetch);
        return $fetch;
    }
    
     public function fetchAssignment($sid){
        $stud = $this->getStudentByID($sid);
        $stud = mysqli_fetch_assoc($stud);
        $batch = $stud['batch'];
        $sql = "SELECT * FROM `assignement` WHERE `batch_name` = '$batch' ORDER BY `id` DESC";
        $fetch = mysqli_query($this->db,$sql);
       
        return $fetch;
    }
    public function fetchAttendance($sid){
        $sql = "SELECT 
                sid,
                course,
                batch,
                COUNT(DISTINCT date) AS total_days, 
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_days, 
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent_days, 
                (SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(DISTINCT date)) * 100 AS attendance_percentage
              FROM attendance
              WHERE sid = '$sid' 
              GROUP BY sid, course";
        $attend = mysqli_query($this->db,$sql);
        return $attend;
    }
}
