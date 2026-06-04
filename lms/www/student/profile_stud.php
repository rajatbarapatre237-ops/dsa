<?php

include "connect/db.php";

$connect = new connect();
$db = $connect->dbconnect();

$id = $_GET['id'] ?? '';

// Direct SQL query to fetch student data
$sql = "SELECT * FROM stud_details WHERE id = '" . $id . "'";
$result = $db->query($sql);
$fetch = $result->fetch_assoc();

// Handle case where student is not found
if (!$fetch) {
    die("Student not found");
}

$student_id = $fetch['id'] ?? '';
$photo = $fetch['pfp'] ?? '';
$name = $fetch['name'] ?? '';
$age = $fetch['age'] ?? '';
$mobile = $fetch['mobile'] ?? '';
$school = $fetch['school_name'] ?? '';
$city = $fetch['city'] ?? '';
$state = $fetch['state'] ?? '';
$course = $fetch['course_name'] ?? '';
$dob = $fetch['dob'] ?? '';
$address = $fetch['address'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .profile-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid white;
            margin: 0 auto 15px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #6b7280;
            overflow: hidden;
        }
        
        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .profile-id {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-content {
            padding: 25px;
        }
        
        .field-group {
            margin-bottom: 20px;
        }
        
        .field-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .field-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .field-label {
            font-weight: 500;
            color: #6b7280;
            width: 30%;
            font-size: 14px;
        }
        
        .field-value {
            color: #374151;
            font-weight: 500;
            width: 65%;
            text-align: right;
            font-size: 14px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3b82f6;
        }
        
        .course-highlight {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        
        .course-highlight .field-value {
            color: #1e40af;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            width: 100%;
        }
        
        @media (max-width: 500px) {
            .field-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .field-label, .field-value {
                width: 100%;
                text-align: left;
            }
            
            .field-value {
                font-size: 15px;
                color: #1f2937;
            }
        }
    </style>
</head>
<body>
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-photo">
                <?php if (!empty($photo)): ?>
                    <img src="../admin/student_pfp/<?php echo htmlspecialchars($photo); ?>" alt="Profile Photo">
                <?php else: ?>
                    <div style="color: #6b7280;">No Photo</div>
                <?php endif; ?>
            </div>
            <div class="profile-name">
                <?php echo htmlspecialchars($name); ?>
            </div>
            <div class="profile-id">
                ID: <?php echo htmlspecialchars($student_id); ?>
            </div>
        </div>
        
        <div class="profile-content">
            <div class="field-group">
                <div class="section-title">Personal Details</div>
                <div class="field-row">
                    <span class="field-label">Age</span>
                    <span class="field-value"><?php echo htmlspecialchars($age); ?> years</span>
                </div>
                <div class="field-row">
                    <span class="field-label">Date of Birth</span>
                    <span class="field-value"><?php echo htmlspecialchars($dob); ?></span>
                </div>
                <div class="field-row">
                    <span class="field-label">Mobile</span>
                    <span class="field-value"><?php echo htmlspecialchars($mobile); ?></span>
                </div>
            </div>
            
            <div class="course-highlight">
                <div class="field-label" style="text-align: center; margin-bottom: 8px; color: #1e40af;">Course</div>
                <div class="field-value"><?php echo htmlspecialchars($course); ?></div>
            </div>
            
            <div class="field-group">
                <div class="section-title">Institution & Location</div>
                <div class="field-row">
                    <span class="field-label">School</span>
                    <span class="field-value"><?php echo htmlspecialchars($school); ?></span>
                </div>
                <div class="field-row">
                    <span class="field-label">City</span>
                    <span class="field-value"><?php echo htmlspecialchars($city); ?></span>
                </div>
                <div class="field-row">
                    <span class="field-label">State</span>
                    <span class="field-value"><?php echo htmlspecialchars($state); ?></span>
                </div>
                <div class="field-row">
                    <span class="field-label">Address</span>
                    <span class="field-value"><?php echo htmlspecialchars($address); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>