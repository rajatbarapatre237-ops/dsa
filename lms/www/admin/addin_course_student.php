<?php 
  include "connect/db.php";
  include "connect/fun.php";
  include 'include/auth_session.php';

  $connect = new connect();
  $fun=new fun($connect->dbconnect());

  $fetch = $fun->fetchAllStudents();
  $course = $fun->getCourseDetails();
  $options = "";
  if(mysqli_num_rows($course)>0){
    while($res= mysqli_fetch_assoc($course)){
        if($res['status']){

            $options.="<option value='".$res['id']."'>".$res['course_name']."</option>";
        }
        
    }
  }
  if(isset($_POST['submit'])){
    [$stud,$course] = $fun->updateCourseStudent($_POST);
    if($stud && $course){
        $msg = "Added!";
    }
    else{
        $msg = "Failed";
    }
    
    header("Location: addin_course_student.php?msg=$msg");
  }

  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add new Course to student</title>
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
        <h1>Add Student to new course</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">Students</li>
            <li class="breadcrumb-item active">Add Student to new course</li>
          </ol>
        </nav>
      </div>
      
      <section class="section">
        <div class="row">
          <div class="col-lg-12">
  
            <div class="card">
            <div class="card-body">
                <h5 class="card-title">Student Table</h5>
                <?php 
                    if(isset($_GET['msg'])){

                    
                ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_GET['msg'];
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php 
                    }
                ?>
                <!-- Table with stripped rows -->
                <div class="table-responsive">
                  <div>

                    <input type="text" class="cd-search table-filter form-control w-25 mb-2 border border-dark rounded-xl" data-table="order-table" placeholder="Item to filter.." />
                  </div>

                    <table class="table  cd-table order-table">
                        <thead>
                        
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">StId</th>
                            <th scope="col">StName</th>
                            <th scope="col">Age</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Course</th>
                            <th scope="col">Batch</th>
                            <th scope="col">Fees</th>
                            <th scope="col">Balance</th>
                            <th scope="col">Add Course</th>
                            <th scope="col"  >Action</th>
                            
                            
                          </tr>
                        </thead>
                        <tbody>
                        <?php 
                            if(mysqli_num_rows($fetch)>0){
                                $sr = 1;
                                while($res = mysqli_fetch_assoc($fetch)){

                                   // print_r($course_taken);
                                
                        ?>
                        <tr>
                          <form action="addin_course_student.php" method="POST">
                          
                            <th scope="row"><?php echo $sr?></th>
                            <td><?php echo "ACE".$res['id']?>
                                <input type="text" name="id" id="<?php echo $res['id']?>" value="<?php echo $res['id']?>" hidden>
                            </td>
                            <td>
                            <input type="text" name="name" id="<?php echo $res['name']?>" value="<?php echo $res['name']?>" hidden>
                              <?php echo $res['name']?>
                            </td>
                            <td><?php echo $res['age']?></td>
                            <td><?php echo $res['mobile']?></td>
                            <td><?php echo $res['course_name']?></td>
                            <td><?php echo $res['batch']?></td>
                            <td><?php echo $res['course_fees']?></td>
                            <td>
                                <input type="text" name="balance"  value="<?php echo $res['balance_fees']?>" hidden>
                                <?php echo $res['balance_fees']?>
                            </td>
                            <td> 
                                <select name="course" id="course" class="form-select">
                                    <option value="">Select Course</option>
                                    <?php echo $options?>
                                </select>
                            </td>
                            
                            <td  >
                              <button type="submit" name="submit" class="btn w-auto btn-info">Add</button>
                            </td>
                            
                          </form>
                        </tr>
                            <?php 
                            $sr++;
                        }
                    }
                    ?>
                    
                        </tbody>
                      </table>
                </div>
               
                <!-- End Table with stripped rows -->
  
              </div>
            </div>
  
          </div>
        </div>
      </section>

      


  </main><!-- End #main -->
  <script>
    (function() {
	'use strict';

var TableFilter = (function() {
 var Arr = Array.prototype;
		var input;
  
		function onInputEvent(e) {
			input = e.target;
			var table1 = document.getElementsByClassName(input.getAttribute('data-table'));
			Arr.forEach.call(table1, function(table) {
				Arr.forEach.call(table.tBodies, function(tbody) {
					Arr.forEach.call(tbody.rows, filter);
				});
			});
		}

		function filter(row) {
			var text = row.textContent.toLowerCase();
       //console.log(text);
      var val = input.value.toLowerCase();
      //console.log(val);
			row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
		}

		return {
			init: function() {
				var inputs = document.getElementsByClassName('table-filter');
				Arr.forEach.call(inputs, function(input) {
					input.oninput = onInputEvent;
				});
			}
		};
 
	})();

  /*console.log(document.readyState);
	document.addEventListener('readystatechange', function() {
		if (document.readyState === 'complete') {
      console.log(document.readyState);
			TableFilter.init();
		}
	}); */
  
 TableFilter.init(); 
})();
  </script>
  <!-- ======= Footer ======= -->
  <?php 
  include "include/footer.php";
 ?>

</body>

</html>