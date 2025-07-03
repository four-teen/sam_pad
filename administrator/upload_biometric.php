<?php
    ob_start();

    include 'db.php';
    require 'vendor/autoload.php'; // Make sure PhpSpreadsheet is installed
    use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['upload_excel']) && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $filename = $_FILES['excel_file']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
        echo "<script>alert('Invalid file type. Please upload a CSV, XLS, or XLSX file.');</script>";
        exit;
    }

    require 'vendor/autoload.php';
    include 'db_connection.php'; // your database connection

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow p-4 border-0 rounded-4">
      <h4 class="mb-4 text-center text-primary">Upload Attendance File</h4>

      <form id="uploadForm" enctype="multipart/form-data" method="POST">
        <div class="mb-3">
          <input class="form-control" type="file" name="excel_file" accept=".csv,.xls,.xlsx" required>
        </div>
        <div class="d-grid">
          <button class="btn btn-primary" type="submit">Upload & Process</button>
        </div>
      </form>

      <div class="progress mt-4 d-none" id="progressContainer">
        <div class="progress-bar progress-bar-striped progress-bar-animated"
             role="progressbar"
             style="width: 0%"
             id="progressBar">0%
        </div>
      </div>

      <div class="alert mt-3 d-none" id="uploadStatus"></div>
    </div>
  </div>
</div>


 

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>    

<script>
document.getElementById("uploadForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const progressBar = document.getElementById("progressBar");
  const progressContainer = document.getElementById("progressContainer");
  const status = document.getElementById("uploadStatus");

  progressContainer.classList.remove("d-none");
  status.classList.add("d-none");
  progressBar.style.width = "0%";
  progressBar.innerText = "0%";

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "manage_attendance.php", true);

  let progress = 0;
  const fakeProgress = setInterval(() => {
    if (progress < 90) {
      progress += 5;
      progressBar.style.width = progress + "%";
      progressBar.innerText = progress + "%";
    }
  }, 200);

xhr.onload = function () {
  clearInterval(fakeProgress);
  progressBar.style.width = "100%";
  progressBar.innerText = "100%";

  if (xhr.status === 200 && xhr.responseText.trim() === "success") {
    status.classList.remove("d-none", "alert-danger");
    status.classList.add("alert-success");
    status.innerText = "File processed successfully! Redirecting...";

    // Redirect after 1 second
    setTimeout(() => {
      window.location.href = "index.php";
    }, 1000);
  } else {
    status.classList.remove("d-none", "alert-success");
    status.classList.add("alert-danger");
    status.innerText = "Error: " + xhr.responseText;
  }
};

  xhr.send(formData);
});
</script>
    
</script>
</body>
</html>
