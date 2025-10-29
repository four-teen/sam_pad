<?php
session_start();
ob_start();
include '../db.php';

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
  <title><?php echo $rowconfig['systemname']; ?> | Presidential Dashboard</title>

  <!-- Favicons -->
  <link href="../assets/img/logo.png" rel="icon">
  <link href="../assets/img/logo.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet">

  <style>
    body {
      background: #f4f6f9;
    }

    /* Center content area since sidebar removed */
    main.main {
      margin-left: 0 !important;
      width: 100% !important;
      padding: 1.5rem;
    }

    /* Responsive row of cards */
    .row.g-2 {
      justify-content: center;
    }

    /* Modern Card Styling */
    .info-card {
      position: relative;
      border-radius: 1rem !important;
      background: linear-gradient(145deg, #ffffff, #f8f9fa);
      transition: all 0.3s ease;
      cursor: pointer;
      overflow: hidden;
    }
    .info-card::before {
      content: "";
      display: block;
      height: 5px;
      border-radius: 5px 5px 0 0;
      background: linear-gradient(90deg, var(--start-color), var(--end-color));
    }
    .info-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }
    .info-card .card-icon {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--start-color), var(--end-color));
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-right: 1rem;
      font-size: 1.75rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      transition: 0.3s ease;
    }
    .info-card:hover .card-icon {
      transform: scale(1.1) rotate(10deg);
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
    }
    .info-card .card-title {
      font-weight: 600;
      font-size: 1rem;
      color: #343a40;
      margin-bottom: 1rem;
    }
    .info-card small {
      font-size: 0.85rem;
      color: #6c757d;
    }
    .info-card h3 {
      font-weight: 700;
      font-size: 1.6rem;
      color: #212529;
    }
    .info-card:hover::after {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: 1rem;
      background: radial-gradient(circle at top right, rgba(255,255,255,0.3), transparent 60%);
      opacity: 0.8;
    }
@media (max-width: 768px) {
  #tbl_pending_actions {
    font-size: 0.85rem;
  }
  .modal-dialog {
    margin: 1rem;
  }
  .modal-body {
    padding: 0.75rem;
  }
}

  </style>
</head>

<body onload="get_req();">
  <?php include 'header.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle text-center mb-4">
      <h1>Presidential Dashboard</h1>
      <p class="text-muted">Document Monitoring and Analytics Overview</p>
    </div>

<section class="section dashboard">
  <div class="container-fluid">
    <div class="row g-3 justify-content-center">

      <!-- 🟦 Pending Actions -->
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="card info-card border-0 shadow-sm" style="--start-color:#007bff;--end-color:#17a2b8;" onclick="show_pending_actions()">
          <div class="card-body">
            <h5 class="card-title">Pending Actions <span class="text-muted">| Awaiting Review</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon"><i class="bx bx-time"></i></div>
              <div>
                <h3 id="load_pending_actions" class="mb-0">0</h3>
                <small class="text-muted">documents for action / approval</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 🟩 Endorsed Documents -->
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="card info-card border-0 shadow-sm" style="--start-color:#198754;--end-color:#20c997;" onclick="show_endorsed_documents()">
          <div class="card-body">
            <h5 class="card-title">Endorsed Documents <span class="text-muted">| Forwarded</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon"><i class="bx bx-send"></i></div>
              <div>
                <h3 id="load_endorsed_documents" class="mb-0">0</h3>
                <small class="text-muted">total endorsed / signed documents</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 🟨 Returned / Deferred -->
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="card info-card border-0 shadow-sm" style="--start-color:#ffc107;--end-color:#ffb347;" onclick="show_returned_deferred()">
          <div class="card-body">
            <h5 class="card-title">Returned / Deferred <span class="text-muted">| For Revision or Clarification</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon"><i class="bx bx-undo"></i></div>
              <div>
                <h3 id="load_returned_deferred" class="mb-0">0</h3>
                <small class="text-muted">returned / under review</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 🟥 Executive Summary -->
      <div class="col-xl-3 col-md-6 col-sm-12">
        <div class="card info-card border-0 shadow-sm" style="--start-color:#dc3545;--end-color:#fd7e14;" onclick="show_executive_summary()">
          <div class="card-body">
            <h5 class="card-title">Executive Summary <span class="text-muted">| Overall Document Statistics</span></h5>
            <div class="d-flex align-items-center">
              <div class="card-icon"><i class="bx bx-bar-chart-alt-2"></i></div>
              <div>
                <h3 id="load_executive_summary" class="mb-0">0</h3>
                <small class="text-muted">total processed / tracked documents</small>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- 📊 Reports Section -->
<!--     <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Document Flow Summary <span class="text-muted">| Real-time Tracking</span></h5>
            <div id="main_data"></div>
          </div>
        </div>
      </div>
    </div> -->

  </div>
</section>

  <br>
  <br>
  <br>
  <br>
  <br>
</main>

  <!-- Footer -->
  <footer id="footer" class="footer text-center" 
    style="
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      width: 100%;
      background: #ffffff;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
      text-align: center;
      padding: 10px 0;
      font-size: 0.9rem;
      color: #555;
      margin: 0;
      z-index: 999;
      box-sizing: border-box;
    ">
    <div class="copyright" style="margin-bottom: 4px; margin-left: auto; margin-right: auto;">
      &copy; <strong><span><?php echo $rowconfig['systemname']; ?></span></strong> All Rights Reserved
    </div>
    <div class="credits" style="margin-left: auto; margin-right: auto;">
      Powered by 
      <a href="#" style="color:#007bff; text-decoration:none;"><?php echo $rowconfig['systemcopyright']; ?></a> | 
      Managed by 
      <a href="https://www.facebook.com/breeve.antonio/" style="color:#007bff; text-decoration:none;">EOA</a>
    </div>
  </footer>

<!-- 🟦 Pending Actions Modal -->
<div class="modal fade" id="modalPendingActions" tabindex="-1" aria-labelledby="modalPendingActionsLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalPendingActionsLabel">
          <i class="bx bx-time"></i> Pending Actions – Documents Received from PAD
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="table-responsive">
          <table id="tbl_pending_actions" class="table table-bordered table-hover table-sm w-100">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Document Code</th>
                <th>Title / Subject</th>
                <th>Originating Office</th>
                <th>Date Received</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- loaded dynamically via AJAX -->
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
          <i class="bi bi-x-circle"></i> Close
        </button>
      </div>

    </div>
  </div>
</div>




  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="../assets/sweetalert2.js"></script>
  <script src="../assets/js/main.js"></script>

  <script>

function show_pending_actions() {
  $('#modalPendingActions').modal('show'); // ✅ show modal on click

  // 🔁 Load data dynamically from PHP (you can adjust query later)
  $.ajax({
    url: "query_president.php",
    type: "POST",
    data: { load_pending_actions: 1 },
    success: function (response) {
      $('#tbl_pending_actions tbody').html(response);
    },
    error: function () {
      $('#tbl_pending_actions tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data.</td></tr>');
    }
  });
}
    
  function animateValue(id, start, end, duration) {
    const el = document.getElementById(id);
    if (!el) return;
    let startTime = null;
    function frame(timestamp) {
      if (!startTime) startTime = timestamp;
      const progress = Math.min((timestamp - startTime) / duration, 1);
      el.textContent = Math.floor(progress * (end - start) + start);
      if (progress < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }
  </script>
</body>
</html>
