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
                    <form action="view_timetable.php" method="post">
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
                            <thead class="table-dark">
                                <tr>
                                    <th>Day/Time</th>
                                    <?php
                                    // Generate column headers dynamically
                                    if (!empty($fetched)) {
                                        $timeHeaders = array_slice($fetched, 0, 4); // Adjust as needed for header count
                                        foreach ($timeHeaders as $time) {
                                            echo "<th class='time-header'>{$time}</th>";
                                        }
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Days of the week
                                $weekDays = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

                                // Determine number of time slots (columns per day)
                                $headerCount = 4; // Number of time headers
                                $totalEntries = count($fetched);
                                $columnsPerDay = $headerCount;

                                // Check if fetched array aligns properly
                                if (($totalEntries - $headerCount) % count($weekDays) !== 0) {
                                    echo "<tr><td colspan='" . ($headerCount + 1) . "'>Invalid timetable data.</td></tr>";
                                } else {
                                    // Generate rows for each day dynamically
                                    foreach ($weekDays as $dayIndex => $day) {
                                        echo "<tr>";
                                        echo "<th>{$day}</th>";

                                        // Calculate the starting index for the current day
                                        $startIndex = $headerCount + ($dayIndex * $columnsPerDay);

                                        // Populate the columns for the current day
                                        for ($i = 0; $i < $columnsPerDay; $i++) {
                                            $value = $fetched[$startIndex + $i] ?? '';
                                            echo "<td class='clickable'>{$value}</td>";
                                        }

                                        echo "</tr>";
                                    }
                                }
                                ?>
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