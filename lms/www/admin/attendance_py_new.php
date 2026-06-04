<?php
include "connect/db.php";
include "connect/fun.php";

function decrypt_aes_cbc($ciphertext_b64) {
    $key = 'ThisIsA16ByteKey';
    $iv = 'ThisIsA16ByteIV!';

    $ciphertext = base64_decode(strtr($ciphertext_b64, '-_', '+/'));
    return openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

$uid = isset($_GET['uid']) ? decrypt_aes_cbc($_GET['uid']) : null;
$rid = isset($_GET['rid']) ? decrypt_aes_cbc($_GET['rid']) : null;

$connection = new connect();
$conn = $connection->dbconnect();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$fun = new fun($conn);
$result = $fun->getStudentBy_UID($uid);

if (!$uid || !$rid) {
    include "templates/unregistered.php";
    exit;
}

if (!$result || $result->num_rows === 0) {
    include "templates/unregistered.php";
    exit;
}

$student = $result->fetch_assoc();
$id = $student['id'];
$batch = $student['batch'];
$course = $student['course_name'];

$attendanceStatus = $fun->markAttendance($id, $course, $batch);

if ($attendanceStatus['status'] == "entry_marked") {
    $statusMessage = "Attendance entry marked!";
    $animationSrc = "https://lottie.host/83273072-209f-41a3-b088-c97dc0239bfc/QWJ3Uqf9gC.lottie";
    $alertClass = "success";
} elseif ($attendanceStatus['status'] == "exit_marked") {
    $statusMessage = "Exit time marked. Attendance is complete!";
    $animationSrc = "https://lottie.host/98294207-911e-4f24-944f-3a13a5376457/KELQrFcGsT.lottie";
    $alertClass = "info";
} elseif ($attendanceStatus['status'] == "wait") {
    $minutesLeft = $attendanceStatus['minutes_left'];
    $statusMessage = "Please wait $minutesLeft minutes before tapping again.";
    $animationSrc = "https://lottie.host/d4ef328c-f63f-439c-82a1-f104150c35b1/ZMa80JJbBv.lottie";
    $alertClass = "warning";
} elseif ($attendanceStatus['status'] == "already_completed") {
    $statusMessage = "Attendance already completed for today!";
    $animationSrc = "https://lottie.host/cb9234e8-7dd8-49dd-9404-050dd862b2e2/mjWtYVwKAe.lottie";
    $alertClass = "secondary";
} else {
    $statusMessage = "Something went wrong. Please try again.";
    $animationSrc = "https://lottie.host/b3e78ef6-51f4-4a53-a729-f155db6e92e1/fUqNaF9rfJ.lottie";
    $alertClass = "danger";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <style>
        body {
            padding-top: 50px;
            background: linear-gradient(135deg, #c2e9fb, #f9f9f9);
            font-family: 'Poppins', sans-serif;
        }

        .alert {
            font-size: 1.25rem;
            border-left: 6px solid;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }

        .alert-success { border-color: #2ecc71; }
        .alert-info { border-color: #3498db; }
        .alert-warning { border-color: #f39c12; }
        .alert-secondary { border-color: #95a5a6; }
        .alert-danger { border-color: #e74c3c; }

        .card {
            border: none;
            border-radius: 1rem;
            background: linear-gradient(to bottom right, #ffffff, #f1f8ff);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .photo {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 50%;
            border: 4px solid #3498db;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .lottie-container {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }

        h5, p {
            margin: 0.5rem 0;
        }

        .name-title {
            font-weight: 600;
            font-size: 1.25rem;
            color: #2c3e50;
        }

        .label {
            font-weight: 500;
            color: #555;
        }

        .value {
            font-weight: 600;
            color: #000;
        }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="alert alert-<?php echo $alertClass; ?> shadow-sm">
        <h4 class="alert-heading mb-0"><?php echo $statusMessage; ?></h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5 text-center">
                        <img src="student_pfp/<?php echo htmlspecialchars($student['pfp']); ?>" class="photo" alt="Student Photo">
                        <!--<img src="../gallery/images/blank-profile-picture-973460_1920.png" class="photo" alt="Student Photo">-->
                    </div>
                    <div class="col-md-7 text-start">
                        <div class="name-title"><?php echo htmlspecialchars($student['name']); ?></div>
                        <p><span class="label">Course:</span> <span class="value"><?php echo htmlspecialchars($course); ?></span></p>
                        <p><span class="label">Batch:</span> <span class="value"><?php echo htmlspecialchars($batch); ?></span></p>
                        <p><span class="label">Date:</span> <span class="value"><?php echo date("Y-m-d H:i:s"); ?></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lottie-container">
        <dotlottie-player 
            src="<?php echo $animationSrc; ?>" 
            background="transparent" 
            speed="1" 
            style="width: 300px; height: 300px;" 
            loop 
            autoplay>
        </dotlottie-player>
    </div>
</div>

<script>
    setTimeout(() => {
        window.close();
    }, 4000);
</script>
</body>
</html>
