<?php
include "connect/db.php"; // Include the database connection class

// Create a connection instance
$conn = new connect();
$db = $conn->dbconnect();

if (!$db) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Check the action type
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'get_teachers') {
        // Fetch distinct teacher emails
        $query = "SELECT  email,name FROM teacher";
        $result = mysqli_query($db, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['email'] . '">' . $row['name'] . '</option>';
        }
    } elseif ($action === 'get_courses' && isset($_POST['email'])) {
        // Fetch courses based on email
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $query = "SELECT  course FROM course_assign WHERE email = '$email'";
        $result = mysqli_query($db, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['course'] . '">' . $row['course'] . '</option>';
        }
    } elseif ($action === 'get_subjects' && isset($_POST['course'])) {
        // Fetch subjects based on course_name
        $course_name = mysqli_real_escape_string($db, $_POST['course']);
        $query = "SELECT `subject_name` FROM `subject` WHERE `course_name` =  '$course_name'";
        $result = mysqli_query($db, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['subject_name'] . '">' . $row['subject_name'] . '</option>';
        }
    } else {
        echo '<option value="">Invalid Request</option>';
    }
} else {
    echo '<option value="">No Action Specified</option>';
}
?>
