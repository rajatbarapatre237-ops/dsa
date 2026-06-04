<aside id="sidebar" class="sidebar">

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link" href="dashboard.php">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-people"></i><span>Students</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="student-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="View_students.php">
          <i class="bi bi-circle"></i><span>View Students</span>
        </a>
      </li>
    </ul>
  </li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#attendance-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-calendar-check"></i><span>Attendance</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="attendance-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="attendance.php">
          <i class="bi bi-circle"></i><span>Add Attendance</span>
        </a>
      </li>
      <li>
        <a href="view_attendance.php">
          <i class="bi bi-circle"></i><span>View Attendance</span>
        </a>
      </li>
    </ul>
  </li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#assign-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-journal-text"></i><span>Assignments</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="assign-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="add_assignment.php">
          <i class="bi bi-circle"></i><span>Add Assignments</span>
        </a>
      </li>
      <li>
        <a href="view_assignment.php">
          <i class="bi bi-circle"></i><span>View Assignments</span>
        </a>
      </li>
      <li>
        <a href="class_test_create.php">
          <i class="bi bi-circle"></i><span>Create Class Test</span>
        </a>
      </li>
      <li>
        <a href="class_test_marks.php">
          <i class="bi bi-circle"></i><span>Enter Class Test Marks</span>
        </a>
      </li>
      <li>
        <a href="class_test_results.php">
          <i class="bi bi-circle"></i><span>Class Test Results</span>
        </a>
      </li>
    </ul>
  </li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#account-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-person-gear"></i><span>Account</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="account-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="view_salary.php">
          <i class="bi bi-circle"></i><span>View Salary</span>
        </a>
      </li>
      <li>
        <a href="change_pass.php">
          <i class="bi bi-circle"></i><span>Change Password</span>
        </a>
      </li>
    </ul>
  </li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="logout.php">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </a>
  </li>

</ul>

</aside>
