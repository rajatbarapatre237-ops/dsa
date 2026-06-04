<!-- <?php
include "connect/db.php";
include "connect/fun.php";

// Create DB connection
$connection = new connect();
$conn = $connection->dbconnect();

// Check DB connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ Step 1: Verify API Token
$headers = getallheaders();
$api_token = $headers['X-Api-Token'] ?? $_GET['api_token'] ?? null;

if (!$api_token) {
    http_response_code(401);
    echo "❌ Unauthorized: API token missing.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM readers WHERE api_token = ? AND is_active = 1");
$stmt->bind_param("s", $api_token);
$stmt->execute();
$readerResult = $stmt->get_result();

if ($readerResult->num_rows === 0) {
    http_response_code(403);
    echo "❌ Unauthorized: Invalid API token.";
    exit;
}

// ✅ Step 2: Continue with Attendance Logic
$fun = new fun($conn);

// Get UID from URL
if (!isset($_GET['id'])) {
    echo "Error: No ID provided.";
    exit;
}

$uid = $_GET['id'];
$result = $fun->getStudentByUID($uid);

if ($result->num_rows === 0) {
    // Unregistered UID
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Unregistered Card</title>
    </head>
    <body style="text-align: center; font-family: Arial, sans-serif; padding-top: 50px; display: grid; place-items: center;">
        <h2>❌ Unregistered Card</h2>
        <p>UID: <?php echo htmlspecialchars($uid); ?></p>
        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
        <dotlottie-player 
            src="https://lottie.host/80dec460-c155-45fe-9624-820d1291f85e/dHgxJijVYK.lottie" 
            background="transparent" 
            speed="1" 
            style="width: 300px; height: 300px" 
            loop 
            autoplay>
        </dotlottie-player>
    </body>
    </html>
    <?php
    exit;
}

$student = $result->fetch_assoc();
$id = $student['id'];
$batch = $student['batch'];
$course = $student['course_name'];

// Mark Attendance and handle different statuses
$attendanceStatus = $fun->markAttendance($id, $course, $batch);

if ($attendanceStatus['status'] == "entry_marked") {
    $statusMessage = "✅ Attendance entry marked!";
    $animationSrc = "https://lottie.host/83273072-209f-41a3-b088-c97dc0239bfc/QWJ3Uqf9gC.lottie";
} elseif ($attendanceStatus['status'] == "exit_marked") {
    $statusMessage = "✅ Exit time marked. Attendance is complete!";
    $animationSrc = "https://lottie.host/83273072-209f-41a3-b088-c97dc0239bfc/QWJ3Uqf9gC.lottie";
} elseif ($attendanceStatus['status'] == "wait") {
    $minutesLeft = $attendanceStatus['minutes_left'];
    $statusMessage = "⏳ Please wait $minutesLeft minutes before tapping again.";
    $animationSrc = "https://lottie.host/80dec460-c155-45fe-9624-820d1291f85e/dHgxJijVYK.lottie";
} elseif ($attendanceStatus['status'] == "already_completed") {
    $statusMessage = "✅ Attendance already completed for today!";
    $animationSrc = "https://lottie.host/83273072-209f-41a3-b088-c97dc0239bfc/QWJ3Uqf9gC.lottie";
} else {
    $statusMessage = "❌ Something went wrong. Please try again.";
    $animationSrc = "https://lottie.host/80dec460-c155-45fe-9624-820d1291f85e/dHgxJijVYK.lottie";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Marked</title>
</head>
<body style="text-align: center; font-family: Arial, sans-serif; padding-top: 50px; display: grid; place-items: center;">
    <h2><?php echo $statusMessage; ?></h2>
    <p>Student: <?php echo htmlspecialchars($student['name']); ?></p>
    <p>Course: <?php echo htmlspecialchars($course); ?></p>
    <p>Batch: <?php echo htmlspecialchars($batch); ?></p>
    <p><?php echo date("Y-m-d H:i:s"); ?></p>

    <!-- Lottie Animation -->
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <dotlottie-player 
        src="<?php echo $animationSrc; ?>" 
        background="transparent" 
        speed="1" 
        style="width: 300px; height: 300px" 
        loop 
        autoplay>
    </dotlottie-player>
</body>
</html>

<?php
$conn->close();
?>

<script>
setTimeout(() => {
  window.close();
}, 4000);
</script>
