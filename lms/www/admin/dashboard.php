<?php 
    include "connect/db.php";        
    include 'connect/fun.php';
    include 'include/auth_session.php';

    $connect=new connect();
    $fun=new fun($connect->dbconnect());

    
    
    $total_records = $fun->countTotalStudents(); 
    $earn  = $fun->getEarning();
   
    $course_query = $fun->getCourseDetails();
    $course_rows = mysqli_num_rows($course_query);
    $intern_rows = $fun->countInterns();

   
    $earnnn = $fun->getEarningData();
    //print_r($earnnn);
    $remain = $fun->getTotalBalanceFees();

    $addReport = $fun->getAddmissionReport();

    $courseData = $fun->getCourseStudentData();
        arsort($courseData);

    $keys = array_keys($courseData);

    $dueSum = $fun->getTotalBalanceFees();
    $totalSum = $fun->getTotalSum();
    $paid = $totalSum - $dueSum;


    $lastTransactions = $fun->fetchLastFiveTransaction();


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard </title>
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
    ?>
  <!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">

            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Students <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $total_records?></h6>
                      <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Revenue <span>| This Month</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        ₹
                    </div>
                    <div class="ps-3">
                      <h6><?php
                            if($earn == 0){
                                echo "0";
                            }
                            else{
                                echo $earn;
                            }
                             ?></h6>
                      <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->

                    </div>
                  </div>

                  
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Courses <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-book"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?php echo $course_rows?></h6>
                      <!-- <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span> -->

                    </div>
                  </div>

                </div>
              </div>

            </div><!-- End Customers Card -->

            <!-- Reports -->
            <div class="col-12">
              <div class="card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Earning Report <span>| Month Wise</span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Earning',
                          data: <?php echo json_encode($earnnn);?>,
                        }, 
                        // {
                        //   name: 'Revenue',
                        //   data: [11, 32, 45, 32, 34, 52, 41]
                        // }, 
                        // {
                        //   name: 'Customers',
                        //   data: [15, 11, 32, 18, 9, 24, 11]
                        // }
                      ],
                        chart: {
                          height: 350,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                        },
                        markers: {
                          size: 4
                        },
                        colors: ['#4154f1', '#2eca6a', '#ff771d'],
                        fill: {
                          type: "gradient",
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                          }
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2
                        },
                        xaxis: {
                          type: 'Months',
                          categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug","Sep","Aug","Nov","Dec"]
                        },
                        tooltip: {
                          x: {
                            format: 'dd/MM/yy HH:mm'
                          },
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->

                </div>

              </div>
            </div><!-- End Reports -->

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Recent Transactions <span>| Last 5</span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">StudentId</th>
                        <th scope="col">Name</th>
                        <th scope="col">Course</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        if(mysqli_num_rows($lastTransactions)>0){
                          $i = 1;
                            while($res = mysqli_fetch_assoc($lastTransactions)){
                            $key = explode("(",$res['reason']);

                      ?>
                          <tr>
                            <th scope="row"><a href="#"><?php echo "#".$i?></a></th>
                            <td><?php echo $res['user_id']?></td>
                            <td><a href="#" class="text-primary"><?php echo $res['name']?></a></td>
                            <td><?php echo $key[0]?></td>
                            <td><span class="badge bg-success"><?php echo $res['amount']?></span></td>
                            <td><?php echo $res['date']?></td>
                          </tr>

                      <?php   
                            $i++;     
                            }
                        }
                      
                      ?>
                      
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

            <!-- Top Selling -->
            <!-- <div class="col-12">
              <div class="card top-selling overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body pb-0">
                  <h5 class="card-title">Top Courses <span>| OverAll</span></h5>

                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Course Name</th>
                        <th scope="col">Students</th>
                       
                      </tr>
                    </thead>
                    <tbody>
                    <?php 
                  
                          for($i = 0; $i<sizeof($keys);$i++){
                            $key = explode("(",$keys[$i]);

                          ?>
                            <tr>
                              
                              
                              <td><?php echo "#".($i+1)?></td>
                              <td class="fw-bold"><?php echo $key[0]?></td>
                              <td><?php echo $courseData[$keys[$i]]?></td>
                            </tr>


                          <?php  
                          }  
                        ?>
                     
                    
                    </tbody>
                  </table>

                </div>

              </div>
            </div>End Top Selling -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">

          

          <!-- Budget Report -->
          <div class="card">
          <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>
            <!-- <div class="card-body pb-0">
              <h5 class="card-title">Student Admission<span>| Month Wise</span></h5>

              
              <canvas id="pieChart" style="max-height: 400px;"></canvas>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  new Chart(document.querySelector('#pieChart'), {
                    type: 'pie',
                    data: {
                      labels: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec',
                      ],
                      datasets: [{
                        label: 'Admitted Students',
                        data: <?php echo json_encode($addReport)?>,
                        backgroundColor: [
                          'rgba(12, 71, 133, 1)',
                          'rgba(214, 0, 46, 1)',
                          'rgba(0, 168, 107, 1)',
                          'rgba(243, 156, 18, 1)',
                          'rgba(186, 104, 200, 1)',
                          'rgba(22, 160, 133, 1)',
                          'rgba(192, 57, 43, 1)',
                          'rgba(230, 126, 34, 1)',
                          'rgba(41, 128, 185, 1)',
                          'rgba(115, 60, 157, 1)',
                          'rgba(243, 156, 18, 1)',
                          'rgba(39, 174, 96, 1)',
                        ],
                        hoverOffset: 4
                      }]
                    }
                  });
                });
              </script>
             

            </div>
          </div>End Budget Report -->

          <!-- Website Traffic -->
          

          <div class="card">
          <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>
            <div class="card-body">
              <h5 class="card-title">Revenue Chart <span>|OverAll</span></h5>

              <!-- Doughnut Chart -->
              <canvas id="doughnutChart" style="max-height: 400px;"></canvas>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  new Chart(document.querySelector('#doughnutChart'), {
                    type: 'doughnut',
                    data: {
                      labels: [
                        'Due',
                        
                        'Paid'
                      ],
                      datasets: [{
                        label: 'rs',
                        data: [<?php echo $dueSum.",".$paid?>],
                        backgroundColor: [
                          'rgb(255, 99, 132)',
                          
                          'rgb(60, 179, 113)'
                        ],
                        hoverOffset: 4
                      }]
                    }
                  });
                });
              </script>
              <!-- End Doughnut CHart -->

            </div>
          </div>
        </div><!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
 <?php 
 include "include/footer.php";
 ?>

</body>

</html>