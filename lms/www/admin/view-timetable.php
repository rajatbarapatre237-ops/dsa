<?php
error_reporting(0);
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$courseee = $fun->getCourseDetailsfromtimetable();
$timetableResult = null; // Initialize $timetableResult to avoid the undefined variable error

// Corrected condition to check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course = $_POST['course'];
    // Fetch timetable based on course
    $timetableResult = $fun->fetchTimetable($course);
    
    if ($timetableResult && mysqli_num_rows($timetableResult) > 0) {
        while ($res = mysqli_fetch_assoc($timetableResult)) {
            $subs = $res['timetable'];
            $fetched = explode(",", $subs);
            $count = count($fetched);
            // print_r($fetched);
        }
    } else {
        echo "No timetable available for the selected course.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Time Table</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <?php
    include "include/links.php";
    ?>

</head>

<body>

    <!-- ======= Header ======= -->
    <?php
    include "include/header.php";
    ?>
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <?php
    include "include/sideBar.php";
    ?><!-- End Sidebar-->

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>View Time Table</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Time Table</li>
                    <li class="breadcrumb-item active">View Time Table</li>
                </ol>
            </nav>
        </div>
        <p class="text-center text-danger"><?php
        if (isset($_GET['msg'])) {
            echo $_GET['msg'];
        }
        ?></p>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <form action="view-timetable.php" method="post">
                        <h2 class="mb-4">Weekly Class Schedule </h2>

                        <div class="d-flex">
                            <select name="course" id="course" class="form-select w-25" onchange="this.form.submit()">
                                <option value="" disabled <?php echo (!isset($_POST['course']) ? 'selected' : ''); ?>>
                                    Select Course</option>
                                <?php
                                if (mysqli_num_rows($courseee) > 0) {
                                    while ($course = mysqli_fetch_assoc($courseee)) {
                                        // Check if the current course is the one selected
                                        $selected = (isset($_POST['course']) && $_POST['course'] == $course['course']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $course['course']; ?>" <?php echo $selected; ?>>
                                            <?php echo $course['course']; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        </form>

                        <div class="table-responsive mt-5">
                        <table class="table table-bordered table-hover">
    <thead class="table-secondary">
        <tr>
            <th>Day/Time</th>
            <?php if(isset($fetched[0]) && trim($fetched[0]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[0] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[1]) && trim($fetched[1]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[1] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[2]) && trim($fetched[2]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[2] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[3]) && trim($fetched[3]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[3] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[4]) && trim($fetched[4]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[4] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[5]) && trim($fetched[5]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[5] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[6]) && trim($fetched[6]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[6] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[7]) && trim($fetched[7]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[7] ?></td>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <tr>
        <?php if(isset($fetched[8]) && trim($fetched[8]) !== ''): ?>
            <th>Monday</th>
            <?php endif; ?>
            <?php if(isset($fetched[8]) && trim($fetched[8]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[8] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[9]) && trim($fetched[9]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[9] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[10]) && trim($fetched[10]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[10] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[11]) && trim($fetched[11]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[11] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[12]) && trim($fetched[12]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[12] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[13]) && trim($fetched[13]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[13] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[14]) && trim($fetched[14]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[14] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[15]) && trim($fetched[15]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[15] ?></td>
            <?php endif; ?>
        </tr>
        <tr>
        <?php if(isset($fetched[16]) && trim($fetched[16]) !== ''): ?>
            <th>Tuesday</th>
            <?php endif; ?>
            <?php if(isset($fetched[16]) && trim($fetched[16]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[16] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[17]) && trim($fetched[17]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[17] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[18]) && trim($fetched[18]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[18] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[19]) && trim($fetched[19]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[19] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[20]) && trim($fetched[20]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[20] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[21]) && trim($fetched[21]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[21] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[22]) && trim($fetched[22]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[22] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[23]) && trim($fetched[23]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[23] ?></td>
            <?php endif; ?>
        </tr>
        <tr>
             <?php if(isset($fetched[24]) && trim($fetched[24]) !== ''): ?>
            <th>Wednesday</th>
            <?php endif; ?>
            <?php if(isset($fetched[24]) && trim($fetched[24]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[24] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[25]) && trim($fetched[25]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[25] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[26]) && trim($fetched[26]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[26] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[27]) && trim($fetched[27]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[27] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[28]) && trim($fetched[28]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[28] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[29]) && trim($fetched[29]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[29] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[30]) && trim($fetched[30]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[30] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[31]) && trim($fetched[31]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[31] ?></td>
            <?php endif; ?>
        </tr>
        <tr>
        <?php if(isset($fetched[32]) && trim($fetched[32]) !== ''): ?>
            <th>Thursday</th>
            <?php endif; ?>
            <?php if(isset($fetched[32]) && trim($fetched[32]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[32] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[33]) && trim($fetched[33]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[33] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[34]) && trim($fetched[34]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[34] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[35]) && trim($fetched[35]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[35] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[36]) && trim($fetched[36]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[36] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[37]) && trim($fetched[37]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[37] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[38]) && trim($fetched[38]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[38] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[39]) && trim($fetched[39]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[39] ?></td>
            <?php endif; ?>
        </tr>
        <tr>
        <?php if(isset($fetched[40]) && trim($fetched[40]) !== ''): ?>
            <th>Friday</th>
            <?php endif; ?>
            <?php if(isset($fetched[40]) && trim($fetched[40]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[40] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[41]) && trim($fetched[41]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[41] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[42]) && trim($fetched[42]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[42] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[43]) && trim($fetched[43]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[43] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[44]) && trim($fetched[44]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[44] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[45]) && trim($fetched[45]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[45] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[46]) && trim($fetched[46]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[46] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[47]) && trim($fetched[47]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[47] ?></td>
            <?php endif; ?>
        </tr>
        <tr>
        <?php if(isset($fetched[48]) && trim($fetched[48]) !== ''): ?>
            <th>Saturday</th>
            <?php endif; ?>
            <?php if(isset($fetched[48]) && trim($fetched[48]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[48] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[49]) && trim($fetched[49]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[49] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[50]) && trim($fetched[50]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[50] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[51]) && trim($fetched[51]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[51] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[52]) && trim($fetched[52]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[52] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[53]) && trim($fetched[53]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[53] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[54]) && trim($fetched[54]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[54] ?></td>
            <?php endif; ?>
            <?php if(isset($fetched[55]) && trim($fetched[55]) !== ''): ?>
                <td class="clickable"><?php echo $fetched[55] ?></td>
            <?php endif; ?>
        </tr>
    </tbody>
</table>

                         



                            
                        </div>
                       
                    
                </div>
    </main>

    <style>
        .clickable {
            cursor: pointer;
        }

        .clickable:hover {
            background-color: #f8f9fa;
        }
    </style>


    </div>
    </div>
    </section>




    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- End #main -->


    
    <!-- ======= Footer ======= -->
    <?php
    include "include/footer.php";
    ?>

</body>

</html>