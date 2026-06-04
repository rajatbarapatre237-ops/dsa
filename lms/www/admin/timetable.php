<?php
error_reporting(0);
include "connect/db.php";
include "connect/fun.php";
include 'include/auth_session.php';

$connect = new connect();
$fun = new fun($connect->dbconnect());

$courseee = $fun->getCourseDetails();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course = trim($_POST['course']);

}
$fetch = $fun->getSubjectDetailsWithCourse($course);
if (isset($_POST['tablesubmit'])) {
    $coursee = ($_POST['course']);
    $table = ($_POST['table']);
    $tab = implode(",", $table);
    $timetable = $fun->insertTimetable($coursee, $tab);

}

if ($timetable) {
    header("Location:view-timetable.php");
}
//   $timetableResult = $fun->fetchTimetable();


//   if (mysqli_num_rows($timetableResult) > 0) {
//       while ($res = mysqli_fetch_assoc($timetableResult)) {

//           $subs = $res['timetable'];


//           $fetch = explode(",", $subs);


//           print_r($fetch); 
//       }
//     }

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
            <h1>Make Time Table</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Front Page Customisation</li>
                    <li class="breadcrumb-item active">Make Time Table</li>
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
                    <form action="timetable.php" method="post">
                        <h2 class="mb-4">Weekly Class Schedule</h2>

                        <div class="d-flex">
                            <select name="course" id="course" class="form-select w-25" onchange="this.form.submit()">
                                <option value="" disabled <?php echo (!isset($_POST['course']) ? 'selected' : ''); ?>>
                                    Select Course</option>
                                <?php
                                if (mysqli_num_rows($courseee) > 0) {
                                    while ($course = mysqli_fetch_assoc($courseee)) {
                                        // Check if the current course is the one selected
                                        $selected = (isset($_POST['course']) && $_POST['course'] == $course['course_name']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $course['course_name']; ?>" <?php echo $selected; ?>>
                                            <?php echo $course['course_name']; ?>
                                        </option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>


                        <div class="table-responsive mt-5">
                        <table id="scheduleTable" class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Day/Time</th>
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                                
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                        <th class="time-header">Enter Time<input type="hidden" name="table[]"
                                                value="" id=""></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Monday</th>
                                        <td class="clickable">Click To Add Subject
                                            <input type="hidden" name="table[]" value="" id="">
                                        </td>
                                        <td class="clickable">Click To Add Subject
                                            <input type="hidden" name="table[]" value="" id="">
                                        </td>
                                        <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                                <td class="clickable">Click To Add Subject
                                            <input type="hidden" name="table[]" value="" id="">
                                        </td>
                                        <td class="clickable">Click To Add Subject
                                            <input type="hidden" name="table[]" value="" id="">
                                        </td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                                
                                    </tr>
                                    <tr>
                                        <th>Tuesday</th>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                                <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                    </tr>
                                    <tr>
                                        <th>Wednesday</th>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                                <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                    </tr>
                                    <tr>
                                        <th>Thursday</th>
                                        <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                                <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                    </tr>
                                    <tr>
                                        <th>Friday</th>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                                <td class="clickable">Click To Add Subject<input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                    </tr>
                                    <tr>
                                        <th>Saturday</th>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                                <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                        <td class="clickable">Click To Add Subject <input type="hidden" name="table[]" value=""
                                                id=""></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="modal fade" id="subjectModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Select Subject</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <select class="form-select" id="subjectSelect">
                                                <option value="" selected>Select Subject</option>
                                                <?php
                                                if (mysqli_num_rows($fetch) > 0) {
                                                    while ($sub = mysqli_fetch_assoc($fetch)) {

                                                        ?>
                                                        <option value="<?php echo $sub['subject_name']; ?>" class="">
                                                            <?php echo $sub['subject_name']; ?>
                                                        </option>
                                                        <?php
                                                    }
                                                }

                                                ?>

                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" onclick="updateSubject()">Save
                                                changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="modal fade" id="timingModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Insert time</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="col-md-12">
                                                <label for="start" class="form-label">Start Timing</label>
                                                <input type="time" class="form-control" id="start" name="start"
                                                    placeholder="Start Timing">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="end" class="form-label">Ending Timing</label>
                                                <input type="time" class="form-control" id="end" name="end"
                                                    placeholder="Ending Time">
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary" id="saveTiming">Save
                                                changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                        <button type="button" class="btn btn-success mt-3" id="addColumnBtn">Add Column</button>
                            <!-- <button type="submit" name="tablesubmit" class="btn btn-primary btn-lg">Submit</button> -->
                            <input type="submit" class="btn btn-lg btn-primary" name="tablesubmit" value="Submit ">
                        </div>
                    </form>
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
    <!-- <script>
        let selectedCell = null;
        const modal = new bootstrap.Modal(document.getElementById('subjectModal'));

        document.querySelectorAll('.clickable').forEach(cell => {
            cell.addEventListener('click', function () {
                selectedCell = this;
                document.getElementById('subjectSelect').value = this.textContent;
                modal.show();
            });
        });

        function updateSubject() {
            if (selectedCell) {
                const newValue = document.getElementById('subjectSelect').value;

                // Update the cell's text content
                selectedCell.textContent = newValue;

                // Create or update the input field
                let input = selectedCell.querySelector('input');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'table[]';
                    input.id = '';
                    selectedCell.appendChild(input);
                }
                input.value = newValue;
            }
            modal.hide();
        }




        document.getElementById('subjectModal').addEventListener('hidden.bs.modal', function () {
            selectedCell = null;
        });



    </script> -->
<script>
   document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('scheduleTable');
    const subjectModal = new bootstrap.Modal(document.getElementById('subjectModal')); // Initialize the modal
    let currentCell = null; // To track the clicked cell or header

    if (!table) {
        console.error('Table with ID "scheduleTable" not found!');
        return;
    }

    // Add column button functionality
    document.getElementById('addColumnBtn').addEventListener('click', function () {
        const headers = table.querySelector('thead tr');
        const rows = table.querySelectorAll('tbody tr');

        // Add new header
        const newHeader = document.createElement('th');
        newHeader.className = 'time-header'; // Add clickable class
        newHeader.innerHTML = 'New Time <input type="hidden" name="table[]" value="">';
        headers.appendChild(newHeader);

        // Add new cell to each row
        rows.forEach(row => {
            const newCell = document.createElement('td');
            newCell.className = 'clickable'; // Add the clickable class
            newCell.innerHTML = 'New Subject <input type="hidden" name="table[]" value="">';
            row.appendChild(newCell);
        });
    });

    // Use event delegation for both headers and cells
    table.addEventListener('click', function (event) {
        const target = event.target;

        // Check if the clicked element is a clickable cell or header
        if (target.classList.contains('clickable')) {
            currentCell = target; // Track the clicked element
            subjectModal.show(); // Open the modal
        }
    });
   

    // Function to update the subject in the clicked element
    window.updateSubject = function () {
        const subjectSelect = document.getElementById('subjectSelect');
        if (currentCell && subjectSelect.value) {
            // Update the element content
            currentCell.innerHTML = `${subjectSelect.value} <input type="hidden" name="table[]" value="${subjectSelect.value}">`;

            // Close the modal
            subjectModal.hide();
        } else {
            alert('Please select a subject before saving.');
        }
    };
});


</script>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('scheduleTable');
    const timingModal = new bootstrap.Modal(document.getElementById('timingModal')); // Initialize the timing modal
    let currentHeader = null; // To track the clicked header

    if (!table) {
        console.error('Table with ID "scheduleTable" not found!');
        return;
    }

   

    // Use event delegation for both headers and cells
    table.addEventListener('click', function (event) {
        const target = event.target;

        // Check if the clicked element is a clickable header
        if (target.classList.contains('time-header')) {
            currentHeader = target; // Track the clicked header
            timingModal.show(); // Open the timing modal
        }
    });

    // Function to update the timing in the clicked header (if necessary)
    const saveButton = document.getElementById('saveTiming');
    if (saveButton) {
        saveButton.addEventListener('click', function () {
            const startTime = document.getElementById('start').value;
            const endTime = document.getElementById('end').value;

            if (currentHeader && startTime && endTime) {
                // Update the header content (you can format it to include time range)
                currentHeader.innerHTML = ` ${startTime} - ${endTime} <input type="hidden" name="table[]" value="${startTime}-${endTime}">`;

                // Close the modal
                timingModal.hide();
            } else {
                alert('Please select both start and end time before saving.');
            }
        });
    }
});


</script>



    <script>

        // Reference to the selected cell
        let selectedHeader = null;

        // Function to handle header click and open the modal
        document.querySelectorAll(".time-header").forEach((header) => {
            header.addEventListener("click", function () {
                selectedHeader = this;

                // Extract the time slot
                const [start, end] = selectedHeader.textContent.trim().split(" - ");

                // Populate modal inputs
                document.getElementById("start").value = start || "";
                document.getElementById("end").value = end || "";

                // Show the modal
                const timingModal = new bootstrap.Modal(document.getElementById("timingModal"));
                timingModal.show();
            });
        });

        // Function to save updates to the time slot
        document.getElementById("saveTiming").addEventListener("click", function () {
            if (selectedHeader) {
                const start = document.getElementById("start").value;
                const end = document.getElementById("end").value;

                if (start && end) {
                    const newValue = `${start} - ${end}`;

                    // Update the header text content
                    selectedHeader.textContent = newValue;

                    // Update the hidden input inside the header
                    let input = selectedHeader.querySelector("input");
                    if (!input) {
                        input = document.createElement("input");
                        input.type = 'hidden';
                        input.name = 'table[]';
                        input.id = '';
                        selectedHeader.appendChild(input);
                    }
                    input.value = newValue;

                    // Hide the modal
                    const timingModal = bootstrap.Modal.getInstance(document.getElementById("timingModal"));
                    timingModal.hide();
                }
            }
        });
    </script>
    <!-- ======= Footer ======= -->
    <?php
    include "include/footer.php";
    ?>

</body>

</html>