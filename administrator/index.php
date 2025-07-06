<?php

    session_start();    
    ob_start();


    include '../db.php';

    if($_SESSION['username']==''){
      header('location:../logout.php');
    }


    $getsystemconfig = "SELECT * FROM `tblconfig`";
    $runsystemconfig=mysqli_query($conn, $getsystemconfig);
    $rowconfig=mysqli_fetch_assoc($runsystemconfig);
    $_SESSION['systemname'] = $rowconfig['systemname'];
    $_SESSION['systemcopyright'] = $rowconfig['systemcopyright'];

    $get_month = "SELECT * FROM `tblsettings` WHERE acc_id='$_SESSION[acc_id]' LIMIT 1";
    $runget_month = mysqli_query($conn, $get_month);
    $rowget_month = mysqli_fetch_assoc($runget_month);

    $mnt = $rowget_month['set_month'];
    $yr = $rowget_month['set_year'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard: <?php echo $rowconfig['systemname'] ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/logo.png" rel="icon">
  <link href="../assets/img/logo.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS (Bootstrap 5 Integration) -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">


  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body onload="get_employees()">



<?php 
  include 'header.php';
  include 'sidebar.php';
 ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Sales Card -->
            <div class="col-lg-4">
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
                  <h5 class="card-title">Employee <span>| Processed</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class='bx bx-user'></i>
                    </div>
                    <div class="ps-3">
                      <h6>
                        <?php 

                        $count = "SELECT distinct emp_no FROM `tbl_biometric_logs`
                        WHERE 
                        accid='$_SESSION[acc_id]'
                        AND curr_month='$mnt'
                        AND curr_year = '$yr'";
                        $getcount = mysqli_query($conn, $count);
                        echo mysqli_num_rows($getcount);
                       ?>
                     </h6>
                      <span class="text-success small pt-1 fw-bold"></span> <span class="text-muted small pt-2 ps-1">record(s)</span>

                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- MANAGE SETTINGS -->
            <div class="col-lg-4" onclick="manage_settings()">
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
                    <a href="#" style="text-decoration: none; color: inherit;">
                      <div class="card-body">
                        <h5 class="card-title">Manage Settings <span>| All</span></h5>
                        <div class="d-flex align-items-center">
                          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class='bx bxs-cog'></i>
                          </div>
                          <div class="ps-3">
                            <h6>
                             <?php 
                                $readable_month = DateTime::createFromFormat('!m', $mnt)->format('F');
                                echo $readable_month . ', ' . $yr;
                             ?> 
                            </h6>
                            <span class="text-danger small pt-1 fw-bold"></span> 
                            <span class="text-muted small pt-2 ps-1">Current Setting</span>
                          </div>
                        </div>
                      </div>
                    </a>
              </div>

            </div>
            <!-- LOAD BIO ATTENDANCE LOG -->
            <div class="col-lg-4">
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
                    <a href="upload_biometric.php" style="text-decoration: none; color: inherit;">
                      <div class="card-body">
                        <h5 class="card-title">Load Bio Attendance <span>| All</span></h5>
                        <div class="d-flex align-items-center">
                          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class='bx bx-log-in-circle'></i>
                          </div>
                          <div class="ps-3">
                            <h6>
                              <?php 

                              $count_all = "SELECT emp_no FROM `tbl_biometric_logs`
                              WHERE 
                              accid='$_SESSION[acc_id]'
                              AND curr_month='$mnt'
                              AND curr_year = '$yr'";
                              $getcount_all = mysqli_query($conn, $count_all);
                              echo mysqli_num_rows($getcount_all);
                             ?> 
                            </h6>
                            <span class="text-danger small pt-1 fw-bold"></span> 
                            <span class="text-muted small pt-2 ps-1">records</span>
                          </div>
                        </div>
                      </div>
                    </a>
              </div>

            </div>

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
                  <h5 class="card-title">Reports <span>/Today</span></h5>
                  <div id="main_data"></div>
                </div>

              </div>
            </div><!-- End Reports -->


          </div>
        </div><!-- End Left side columns -->

        <div class="modal fade" id="modal_class" tabindex="-1" aria-labelledby="modalLabel1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalLabel1">Update Class</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" id="emp_nums">
                <div class="row">
                  <div class="col-lg-12">
                    <label for="emp_classes">Select Class</label>
                    <select id="emp_classes" class="form-control">
                      <option value="0">Teaching</option>
                      <option value="1">Non-Teaching</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 py-2">
                    <button onclick="saving_class()" class="btn btn-info btn-sm text-end">Update Class</button>
                  </div>
                </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="modal_settings" tabindex="-1" aria-labelledby="modalLabel1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalLabel1">Update Settings</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" id="emp_nums">
                <div class="row">
                  <div class="col-lg-6">
                    <label for="set_month">Month</label>
                    <select id="set_month" class="form-control">
                      <option value="1">January</option>
                      <option value="2">February</option>
                      <option value="3">March</option>
                      <option value="4">April</option>
                      <option value="5">May</option>
                      <option value="6">June</option>
                      <option value="7">July</option>
                      <option value="8">August</option>
                      <option value="9">September</option>
                      <option value="10">October</option>
                      <option value="11">November</option>
                      <option value="12">December</option>
                    </select>
                  </div>

                  <div class="col-lg-6">
                    <label for="set_year">Year</label>
                    <input type="number" id="set_year" class="form-control" value="<?= date('Y') ?>">
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 py-2 d-flex justify-content-end">
                    <button onclick="update_settings()" class="btn btn-info btn-sm" data-bs-dismiss="modal">
                      Update Settings
                    </button>
                  </div>
                </div>
              </div>
              <div class="modal-footer" style="justify-content: flex-start;">
                <div class="row w-100 m-0">
                  <div class="col-lg-12">
                    <?php 
                      // Convert to readable format
                      $readable_month = DateTime::createFromFormat('!m', $mnt)->format('F');
                    ?>
                    <h5 class="mb-0">CURRENT SETTINGS: <?php echo "$readable_month, $yr"; ?></h5>                    
                  </div>
                </div>
              </div>


             
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Mnemon</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="#">Mnemon</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (Required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
   <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>


<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

  <script>

    function update_settings(){
      var set_month = $('#set_month').val();
      var set_year = $('#set_year').val();

       $.ajax({
          type: "POST",
          url: "query_employee.php",
          data: {
            "save_settings": "1",
            "set_month" : set_month,
            "set_year" : set_year           
          },
          success: function (response) {

          }
        }); 
    }

    function manage_settings(){
      $('#modal_settings').modal('show');
    }

    function saving_class(){
      var emp_nums =  $('#emp_nums').val(); 
      var emp_classes =  $('#emp_classes').val(); 

       $.ajax({
          type: "POST",
          url: "query_employee.php",
          data: {
            "save_classed": "1",
            "emp_nums" : emp_nums,
            "emp_classes" : emp_classes           
          },
          success: function (response) {
              get_employees();
              // $('#test').html(response);
          }
        });        
    }

    function set_class(emp_no){
      $('#emp_nums').val(emp_no);      
      $('#modal_class').modal('show');
    }



    function saving_shift(){
      var shifts = $('#shifts').val();
      var acno = $('#acno').val();

       $.ajax({
          type: "POST",
          url: "query_employee.php",
          data: {
            "save_shift": "1",
            "AC_No" : acno,
            "shifts" : shifts           
          },
          success: function (response) {
              get_employees();
              $('#test').html(response);
          }
        });       
    }

function get_employees() {
    let progress = 0;
    let interval;

    // Insert the stylish striped progress bar
    $('#main_data').html(`
        <div style="padding: 1rem;">
            <div class="progress" style="height: 6px; background-color: #e9ecef;">
                <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar"
                     style="width: 0%; background: linear-gradient(90deg, #17a2b8, #0dcaf0);">
                </div>
            </div>
            <div id="progress-label" style="font-size: 11px; margin-top: 6px; color: #6c757d;">
                Loading biometric logs... 0%
            </div>
        </div>
    `);

    // Animate progress bar up to 90%
    interval = setInterval(() => {
        if (progress < 90) {
            progress++;
            $('#progress-bar').css('width', progress + '%');
            $('#progress-label').text(`Loading biometric logs... ${progress}%`);
        }
    }, 20);

    // AJAX load employee data
    $.ajax({
        type: "POST",
        url: "query_employee.php",
        data: { "loading_employee": "1" },
        success: function (response) {
            clearInterval(interval);
            $('#progress-bar').css('width', '100%');
            $('#progress-label').html(`<i class="bx bx-check-circle text-success"></i> Load complete!`);

            // Fade out the loader then show table
            setTimeout(() => {
                $('#main_data').html(response);
                $('#payrollTable').DataTable({
                    paging: true,
                    pageLength: 10,
                    lengthChange: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false
                });
            }, 600);
        }
    });
}






  </script>

</body>

</html>