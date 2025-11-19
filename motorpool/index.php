<?php
ob_start();              // Optional but good for safety
session_start();         // Start session before anything else
include '../db.php';     // Then include database or other files

if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
    header('location:../logout.php');
    exit;
}

$getsystemconfig = "SELECT * FROM `tblconfig`";
$runsystemconfig = mysqli_query($conn, $getsystemconfig);
$rowconfig = mysqli_fetch_assoc($runsystemconfig);
$_SESSION['systemname'] = $rowconfig['systemname'];
$_SESSION['systemcopyright'] = $rowconfig['systemcopyright'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo $rowconfig['systemname']; ?> | Dashboard</title>

  <!-- Favicons -->
  <link href="../assets/img/logo.png" rel="icon">
  <link href="../assets/img/logo.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- ✅ Bootstrap 5 theme for Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="css_index.css" rel="stylesheet">

  <style>

/*      .card:hover {
          transform: scale(1.01);
          transition: 0.2s;
      }*/



  </style>

</head>

<body>

  <?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>

  <main id="main" class="main">
    <section class="section dashboard">
      <div class="row">

        <!-- Reports -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                  Received Documents <span class="text-muted">/ Processing...</span>
                </h5>
                <button id="btnAddRecord" class="btn btn-primary shadow-sm">
                  <i class="bi bi-file-earmark-plus"></i> Add New Record
                </button>
              </div>

              <div id="main_data"></div>
            </div>
          </div>
        </div>
      </div>
    </section>


  </main>


<!-- //manage employees -->
<div class="modal fade" id="VehicleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uploadImagesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-semibold" id="uploadImagesLabel">
          <i class="bi bi-images me-2"></i> Manage Vehicles
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12">
            <label for="plate_number">Plate Number</label>
            <input type="text" class="form-control" id="plate_number">
          </div>
          <div class="col-lg-12">
            <label for="vehicle">Vehicle</label>
            <input type="text" class="form-control" id="vehicle">
          </div>
        </div>
        <div class="mb-3 py-2">
            <button type="button" class="btn btn-primary" onclick="saving_vehicle()">
                <i class="bi bi-save2"></i> Add to list...
            </button>
        </div>  
        <div class="mb-3 py-2">
          <div id="vehicle_list">Loading list...</div>
        </div>               
      </div>
      <div class="modal-footer bg-white border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>



  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; <strong><span><?php echo $rowconfig['systemname']; ?></span></strong> All Rights Reserved
    </div>
    <div class="credits">
      Powered by <a href="#"><?php echo $rowconfig['systemcopyright']; ?></a><br>Managed by <a href="https://www.facebook.com/breeve.antonio/">EOA</a>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="../assets/sweetalert2.js"></script>
  <script src="../assets/js/main.js"></script>
<script>

// =============================================================================================
// SECTION 2
// =============================================================================================
  function delete_vehicle(id) {
      Swal.fire({
          title: "Delete Vehicle?",
          text: "This action cannot be undone.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#dc3545",
          confirmButtonText: "Delete",
          cancelButtonText: "Cancel"
      }).then((result) => {
          if (result.isConfirmed) {
              $.post("query_records.php", { delete_vehicle: 1, id: id }, function(response){
                    loading_vehicles();
                  if (response.trim() === "success") {
                      loading_vehicles();
                  }
              });
          }
      });
  }  

  function saving_vehicle(){
    const plate_number = $('#plate_number').val();
    const vehicle = $('#vehicle').val();
    $.ajax({
      url: "query_records.php",
      type: "POST",
      data: { 
        saving_vehicle_records: 1 ,
        plate_number : plate_number,
        vehicle : vehicle
      },
      success: function(response) {
        $('#plate_number').val('');
        $('#vehicle').val('');  
        $('#plate_number').focus();      
        loading_vehicles();
      }
    });

  }

  function manage_vehicle(){
    loading_vehicles();
    $('#VehicleModal').modal('show');
  }

  function loading_vehicles(){
    $.ajax({
      url: "query_records.php",
      type: "POST",
      data: { 
        get_vehicle_records: 1 
      },
      success: function(response) {
        $('#vehicle_list').html(response);
      }
    });
  }


// =============================================================================================
// SECTION 1
// =============================================================================================
  function loadingData() {
    $.ajax({
      url: "query_records.php",
      type: "POST",
      data: { 
        get_travels: 1 
      },
      success: function(response) {
        $('#main_data').html(response);
      }
    });
  }


// =============================================================================================
// SECTION 2
// =============================================================================================
window.onload = function() {
  loadingData();
};



</script>



</body>

</html>
