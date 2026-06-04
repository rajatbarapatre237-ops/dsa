<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "connect/db.php"; // Include your database connection file

    $class = $_POST['class'];
    $response = '';
print_r($class);
    if (!empty($class)) {
        $query = "SELECT * FROM `batches` WHERE course =?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $class);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                 //$response .= '<option value="' . htmlspecialchars($row['name']) . '">' . htmlspecialchars($row['name']) . '</option>';
               $response .= '<option value="1">Batch 1</option><option value="2">Batch 2</option>';
            }
        } else {
            $response .= '<option value="">No batches available</option>';
        }
        $stmt->close();
    } else {
        $response .= '<option value="">Select Batch</option>';
    }

    echo $response;
}
?>
