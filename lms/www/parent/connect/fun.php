<?php
class fun
{
    private $db;
    function __construct($con)
    {
        $this->db = $con;

    }

    // public function parentlogin($id,$password){
    //     session_start();
        
    //     $query    = "SELECT * FROM `users` WHERE `sid`='$id' AND `pass` = '$password'";
    //     $result = mysqli_query($this->db, $query);

        
    //     $rows = mysqli_num_rows($result);
    //     if ($rows == 1) {
           
    //         $_SESSION['student_id'] = $id;
    //         header("Location: parent_dashboard.php");
            
    //       return $result;
    //         // Redirect to user dashboard page
           
             
    //     }
    //     else{
    //         header("Location: index.php");
    //     }
    // }

 public function parentlogin($id, $password) {
    $stmt = $this->db->prepare("SELECT * FROM parent WHERE id = ? AND pass = ?");
    $stmt->bind_param("ss", $id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
  }

    function getTodayAttendance($student_id) {
        $today = date('Y-m-d');
    
       
       
    
        $sql = "SELECT entry_time, exit_time, status FROM attendance WHERE `sid` = '$student_id' AND `date` = '$today'";
        $result = mysqli_query($this->db, $sql);
    
        $entry_time = "Not Available";
        $exit_time = "Not Available";
    
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $entry_time = $row['entry_time'] ?: "Not Available";
            $exit_time = $row['exit_time'] ?: "Not Available";
            $status = $row['status'] ?: "";
        }
    
        return [
            'entry_time' => $entry_time,
            'exit_time' => $exit_time,
            'date' => $today,
            'status'=>$status
        ];
    }
    
     function getstudname($student_id) {
        $query    = "SELECT `name` FROM `stud_details` WHERE `id`='$student_id' ";
        $result = mysqli_query($this->db, $query);
         if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $stud_name = $row['name'] ?: "";
           
        }
    
        return [
            'stud_name' => $stud_name
     
        ];
    }
    
public function getParentByID($id) {
    $stmt = $this->db->prepare("SELECT * FROM parent WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function updateParentPassword($id, $newPassword) {
    $stmt = $this->db->prepare("UPDATE parent SET pass = ? WHERE id = ?");
    return $stmt->execute([$newPassword, $id]);
}


}