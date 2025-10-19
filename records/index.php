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

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

  <style>
      /* Fix select2 alignment */
      .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        padding: 4px 8px !important;
      }

      .select2-container--default .select2-selection__rendered {
        line-height: 28px !important;
        font-size: 0.95rem !important;
        color: #495057 !important;
      }

      .select2-container--default .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
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

      /* Icon Circle */
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

      /* Title and Text */
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

      /* Animation for hover glow */
      .info-card:hover::after {
      content: "";
      position: absolute;
      inset: 0;
      border-radius: 1rem;
      background: radial-gradient(circle at top right, rgba(255,255,255,0.3), transparent 60%);
      opacity: 0.8;
      }
  </style>
</head>

<body onload="get_req();">

  <?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
<!-- Improved Dashboard Cards -->
<div class="row g-2">

  <!-- Manage Request -->
  <div class="col-lg-3 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#007bff;--end-color:#17a2b8;" onclick="show_request_entry()">
      <div class="card-body">
        <h5 class="card-title">Manage Request <span class="text-muted">| Requests</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bx bx-file"></i>
          </div>
          <div>
            <h3 id="load_requests" class="mb-0">0</h3>
            <small class="text-muted">pending / processed</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Manage Document Types -->
  <div class="col-lg-3 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#198754;--end-color:#20c997;" onclick="manage_doc_types()">
      <div class="card-body">
        <h5 class="card-title">Manage Document Type <span class="text-muted">| Documents</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bx bx-book"></i>
          </div>
          <div>
            <h3 id="load_doc_types" class="mb-0">0</h3>
            <small class="text-muted">available document types</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Released Summary -->
  <div class="col-lg-3 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#ffc107;--end-color:#ffb347;" onclick="released_summary()">
      <div class="card-body">
        <h5 class="card-title">Summary <span class="text-muted">| Released</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bx bxs-objects-vertical-top"></i>
          </div>
          <div>
            <h3 id="load_summary" class="mb-0">0</h3>
            <small class="text-muted">transactions this period</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics -->
  <div class="col-lg-3 col-md-6">
    <div class="card info-card border-0 shadow-sm" style="--start-color:#dc3545;--end-color:#fd7e14;" onclick="statistics_summary()">
      <div class="card-body">
        <h5 class="card-title">Statistics <span class="text-muted">| Documents</span></h5>
        <div class="d-flex align-items-center">
          <div class="card-icon">
            <i class="bx bx-bar-chart-alt-2"></i>
          </div>
          <div>
            <h3 id="load_statistics" class="mb-0">0</h3>
            <small class="text-muted">total transactions</small>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>


        <!-- Reports -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Recorded Requests <span class="text-muted">/ Processing...</span></h5>
              <div id="main_data"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; <strong><span><?php echo $rowconfig['systemname']; ?></span></strong> All Rights Reserved
    </div>
    <div class="credits">
      Powered by <a href="#"><?php echo $rowconfig['systemcopyright']; ?></a> | Managed by <a href="https://www.facebook.com/breeve.antonio/">EOA</a>
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

function animateValue(id, start, end, duration) {
  const el = document.getElementById(id);
  if (!el) return; // prevent errors if element not found
  let startTime = null;
  function frame(timestamp) {
    if (!startTime) startTime = timestamp;
    const progress = Math.min((timestamp - startTime) / duration, 1);
    el.textContent = Math.floor(progress * (end - start) + start);
    if (progress < 1) requestAnimationFrame(frame);
  }
  requestAnimationFrame(frame);
}

    function get_req() {
      let progress = 0;
      let interval;

      $('#main_data').html(`
        <div style="padding: 1rem;">
          <div class="progress" style="height: 6px;">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
              style="width: 0%; background: linear-gradient(90deg, #17a2b8, #0dcaf0);"></div>
          </div>
          <div id="progress-label" style="font-size: 11px; margin-top: 6px; color: #6c757d;">
            Loading data... 0%
          </div>
        </div>`);

      interval = setInterval(() => {
        if (progress < 90) {
          progress++;
          $('#progress-bar').css('width', progress + '%');
          $('#progress-label').text(`Loading data... ${progress}%`);
        }
      }, 20);

      $.ajax({
        type: "POST",
        url: "query_user_account.php",
        data: { "loading_users": "1" },
        success: function(response) {
          clearInterval(interval);
          $('#progress-bar').css('width', '100%');
          $('#progress-label').html(`<i class="bx bx-check-circle text-success"></i> Load complete!`);

          setTimeout(() => {
            $('#main_data').html(response);
            $('#requestTable').DataTable({
              paging: true,
              pageLength: 10,
              lengthChange: true,
              searching: true,
              ordering: true,
              info: true,
              autoWidth: false
            });

  // Animate the card counters here
  animateValue("load_requests", 0, 25, 800);
  animateValue("load_doc_types", 0, 8, 800);
  animateValue("load_summary", 0, 12, 800);
  animateValue("load_statistics", 0, 45, 800);

          }, 600);
        }
      });
    }
  </script>
</body>

</html>
