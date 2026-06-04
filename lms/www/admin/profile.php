<?php
// Check if 'id' is present in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Optional: You can validate or sanitize the id here
    // $id = intval($id); // Converts to integer

    // Redirect to another page, e.g., view.php with the same id
    header("Location: ../student/profile_stud.php?id=" . urlencode($id));
    exit(); // Always exit after using header redirect
}
?>


