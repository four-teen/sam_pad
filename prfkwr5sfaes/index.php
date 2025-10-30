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
.doc-card {
  border-left: 4px solid #007bff;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.doc-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
@media (max-width: 768px) {
  .doc-card {
    font-size: 0.9rem;
    padding: 10px;
  }
}
.doc-card {
  border-left: 4px solid #007bff;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer; /* 👈 make cards clickable */
}
.doc-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
#document_image_container {
  max-height: 70vh;
  overflow-y: auto;
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
<div id="test">test</div>
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
          <i class="bx bx-time"></i> Pending Actions
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="pending_list_container" style="max-height:70vh; overflow-y:auto; padding:5px;">
          <!-- cards load here -->
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

<!-- 📄 Document Image Viewer Modal -->
<div class="modal fade" id="modalViewImage" tabindex="-1" aria-labelledby="modalViewImageLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalViewImageLabel"><i class="bi bi-image"></i> Document Attachment</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center" id="document_image_container">
        <div class="text-muted">Loading document image...</div>
      </div>
    </div>
  </div>
</div>

<!-- 🖋️ Image Annotation Modal -->
<div class="modal fade" id="annotateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title"><i class="bi bi-brush"></i> Annotate Image</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <canvas id="annotateCanvas" style="max-width:100%; border:1px solid #ddd; cursor:crosshair;"></canvas>
<div class="mt-3">
  <div class="d-flex justify-content-center gap-2 flex-wrap mb-2">
    <button class="btn btn-sm btn-outline-secondary" id="drawModeBtn"><i class="bi bi-pencil"></i> Draw</button>
    <button class="btn btn-sm btn-outline-secondary" id="textModeBtn"><i class="bi bi-type"></i> Text</button>
    <button class="btn btn-sm btn-outline-warning" id="clearCanvasBtn"><i class="bi bi-eraser"></i> Clear</button>
    <button class="btn btn-sm btn-success" id="saveAnnotatedBtn"><i class="bi bi-save"></i> Save</button>
  </div>

  <!-- 🗒️ Fixed Annotation Textbox with Add Button -->
  <div class="d-flex justify-content-center align-items-start gap-2 flex-wrap" style="max-width:650px; margin:auto;">
    <textarea id="bottomAnnotationBox" class="form-control form-control-sm flex-grow-1"
      placeholder="Type your remarks here..."
      style="resize:none; height:80px; background:rgba(255,255,255,0.9); border:1px solid #ccc; border-radius:6px;"></textarea>
    <button class="btn btn-sm btn-primary mt-2 mt-md-0" id="addAnnotationBtn">
      <i class="bi bi-plus-circle"></i> Add Annotation
    </button>
  </div>
</div>
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

// ==============IMAGE ANNOTATION===================================

let canvas, ctx, imgObj;
let isDrawing = false;
let mode = 'draw'; // draw | text
let currentImgPath = '';
let currentImgId = 0;

function openAnnotateModal(imgPath, imgId) {
  currentImgPath = imgPath;
  currentImgId = imgId;

  $('#annotateModal').modal('show');

  setTimeout(() => {
    canvas = document.getElementById('annotateCanvas');
    ctx = canvas.getContext('2d');
    imgObj = new Image();

    imgObj.onload = function () {
      canvas.width = imgObj.width;
      canvas.height = imgObj.height;
      ctx.drawImage(imgObj, 0, 0);
    };
    imgObj.src = imgPath;

    /* ===================== COORDINATE HELPER ===================== */
    function getCoords(e) {
      const rect = canvas.getBoundingClientRect();
      if (e.touches && e.touches.length > 0) {
        return {
          x: e.touches[0].clientX - rect.left,
          y: e.touches[0].clientY - rect.top,
        };
      } else {
        return {
          x: e.offsetX ?? e.clientX - rect.left,
          y: e.offsetY ?? e.clientY - rect.top,
        };
      }
    }

    /* ===================== DRAW MODE (Mouse + Touch) ===================== */
    canvas.onmousedown = (e) => {
      const { x, y } = getCoords(e);
      if (mode === "draw") {
        isDrawing = true;
        ctx.beginPath();
        ctx.moveTo(x, y);
      }
    };

    canvas.onmousemove = (e) => {
      if (isDrawing && mode === "draw") {
        const { x, y } = getCoords(e);
        ctx.lineTo(x, y);
        ctx.strokeStyle = "blue";
        ctx.lineWidth = 2;
        ctx.stroke();
      }
    };

    canvas.onmouseup = () => (isDrawing = false);

    // Touch events for drawing
    canvas.addEventListener("touchstart", (e) => {
      if (mode !== "draw") return;
      const { x, y } = getCoords(e);
      isDrawing = true;
      ctx.beginPath();
      ctx.moveTo(x, y);
      e.preventDefault();
    });

    canvas.addEventListener("touchmove", (e) => {
      if (isDrawing && mode === "draw") {
        const { x, y } = getCoords(e);
        ctx.lineTo(x, y);
        ctx.strokeStyle = "blue";
        ctx.lineWidth = 2;
        ctx.stroke();
      }
      e.preventDefault();
    });

    canvas.addEventListener("touchend", () => (isDrawing = false));

    /* ===================== TEXT MODE (Bottom Annotation Box) ===================== */
    $("#textModeBtn").click(() => (mode = "text"));

function renderBottomText() {
  const text = $("#bottomAnnotationBox").val().trim();
  if (text === "") return;

  const padding = 10;
  const lineHeight = 22;
  const maxWidth = canvas.width - 2 * padding;

  // 🔹 Split by new lines first (\n from textarea)
  const paragraphs = text.split(/\r?\n/);
  ctx.font = "18px Poppins, Arial";
  let lines = [];

  // 🔹 Wrap each paragraph individually
  paragraphs.forEach((para) => {
    const words = para.split(" ");
    let currentLine = words[0];
    for (let i = 1; i < words.length; i++) {
      const testLine = currentLine + " " + words[i];
      if (ctx.measureText(testLine).width > maxWidth && i > 0) {
        lines.push(currentLine);
        currentLine = words[i];
      } else {
        currentLine = testLine;
      }
    }
    lines.push(currentLine);
  });

  // 🔹 Calculate box height based on total lines
  const boxHeight = lines.length * lineHeight + padding * 2;
  const y = canvas.height - boxHeight - 10;

  // 🔹 Draw gray background box
  ctx.fillStyle = "rgba(200,200,200,0.8)";
  ctx.fillRect(padding, y, canvas.width - 2 * padding, boxHeight);
  ctx.strokeStyle = "rgba(100,100,100,0.4)";
  ctx.strokeRect(padding, y, canvas.width - 2 * padding, boxHeight);

  // 🔹 Render wrapped + multi-line text
  ctx.fillStyle = "red";
  ctx.textBaseline = "top";
  let textY = y + padding;
  lines.forEach((line) => {
    ctx.fillText(line, padding + 10, textY);
    textY += lineHeight;
  });
}


    // ✅ When pressing Enter in bottom textbox, render annotation
// 🔹 Add button click handler for annotation
$("#addAnnotationBtn").off("click").on("click", function () {
  renderBottomText();
  $("#bottomAnnotationBox").val(""); // clear textarea after adding
});

  }, 300); // end of setTimeout
}

/* ===================== TOOLBAR BUTTONS ===================== */
$("#drawModeBtn").click(() => (mode = "draw"));
$("#textModeBtn").click(() => (mode = "text"));
$("#clearCanvasBtn").click(() => {
  ctx.drawImage(imgObj, 0, 0);
});
$("#saveAnnotatedBtn").click(() => saveAnnotatedImage());

/* ===================== SAVE ANNOTATED IMAGE ===================== */
function saveAnnotatedImage() {
  const dataURL = canvas.toDataURL("image/png");
  $.ajax({
    url: "query_president.php",
    type: "POST",
    data: { image: dataURL, img_id: currentImgId },
    success: function (res) {
      Swal.fire({
        title: "Saved!",
        text: res,
        icon: "success",
        timer: 1500,
        showConfirmButton: false,
      });
      $("#annotateModal").modal("hide");
    },
    error: function (xhr) {
      Swal.fire("Error", xhr.responseText || "Unable to save image", "error");
    },
  });
}

//==========END OF IMAGE ANNOTATION===============================



// 🖼️ Load image related to clicked document
function viewDocumentImage(docId) {
  $('#modalViewImage').modal('show');
  $('#document_image_container').html('<div class="text-muted py-4">Loading document image...</div>');

  $.ajax({
    url: "query_president.php",
    type: "POST",
    data: { get_document_image: 1, doc_id: docId },
    success: function (response) {
      $('#document_image_container').html(response);
    },
    error: function () {
      $('#document_image_container').html('<div class="text-danger py-4">Error loading image.</div>');
    }
  });
}

function show_pending_actions() {
  $('#modalPendingActions').modal('show');

  $("#pending_list_container").html('<div class="text-center text-secondary py-3">Loading...</div>');

  $.ajax({
    url: "query_president.php",
    type: "POST",
    data: { load_pending_actions: 1 },
    success: function (response) {
      $("#pending_list_container").html(response);
    },
    error: function () {
      $("#pending_list_container").html('<div class="text-center text-danger py-3">Error loading data.</div>');
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

// 🔹 Load the count for the Pending Actions card
function get_req() {
  $.ajax({
    url: "query_president.php",
    type: "POST",
    data: { get_received_counter: 1 },
    success: function (response) {
      $("#load_pending_actions").text(response || 0);
    },
    error: function () {
      $("#load_pending_actions").text("0");
    }
  });
}


  </script>
</body>
</html>
