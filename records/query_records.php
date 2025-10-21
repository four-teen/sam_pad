<?php
include '../db.php';
session_start();


if (isset($_POST['take_action'])) {
    $doc_id = $_POST['doc_id'];
    $from_office_id = $_SESSION['office_id'] ?? null; // If you track the current user’s office
    $to_office_id = $_POST['to_office_id'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    $acted_by = $_SESSION['username'];

    $insert = "
        INSERT INTO tbl_document_actions 
        (doc_id, from_office_id, to_office_id, action_type, action_remarks, action_status, acted_by)
        VALUES ('$doc_id', '$from_office_id', '$to_office_id', 'Outgoing', '$remarks', 'Pending', '$acted_by')
    ";

    echo mysqli_query($conn, $insert) ? 'success' : 'error';
    exit;
}

/* 🔹 UPLOAD IMAGES */
if (isset($_POST['upload_images'])) {
    $doc_id = intval($_POST['doc_id']);
    if ($doc_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid document.']);
        exit;
    }

    if (!isset($_FILES['images'])) {
        echo json_encode(['status' => 'error', 'message' => 'No files received.']);
        exit;
    }

    $uploadDir = dirname(__DIR__) . '/uploads/'; // filesystem path
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }

    $allowed = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
    $maxSize = 5 * 1024 * 1024;

    $files = $_FILES['images'];
    $count = count($files['name']);
    $uploaded = 0; $errors = [];

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = $files['name'][$i] . ' failed to upload.';
            continue;
        }

        // Validate size
        if ($files['size'][$i] > $maxSize) {
            $errors[] = $files['name'][$i] . ' exceeds 5MB.';
            continue;
        }

        // Validate mime using finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($files['tmp_name'][$i]);
        if (!in_array($mime, $allowed)) {
            $errors[] = $files['name'][$i] . ' is not an allowed image type.';
            continue;
        }

        // Create safe unique filename
        $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
        $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
        $dest = $uploadDir . $newName;

        if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
            // Save to DB
            $stmt = mysqli_prepare($conn, "INSERT INTO tbl_document_images (doc_id, img_filename) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "is", $doc_id, $newName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $uploaded++;
        } else {
            $errors[] = $files['name'][$i] . ' could not be saved.';
        }
    }

    echo json_encode([
        'status'  => 'ok',
        'uploaded'=> $uploaded,
        'errors'  => $errors
    ]);
    exit;
}


/* 🔹 DELETE IMAGE */
if (isset($_POST['delete_image'])) {
    $img_id = intval($_POST['img_id']);

    $get = mysqli_query($conn, "SELECT img_filename FROM tbl_document_images WHERE img_id = $img_id");
    if ($get && mysqli_num_rows($get) === 1) {
        $row = mysqli_fetch_assoc($get);
        $file = $row['img_filename'];

        // Delete DB row first
        mysqli_query($conn, "DELETE FROM tbl_document_images WHERE img_id = $img_id");

        // Remove file on disk
        $path = dirname(__DIR__) . '/uploads/' . $file;
        if (is_file($path)) { @unlink($path); }

        echo "deleted";
    } else {
        echo "not_found";
    }
    exit;
}


/* 🔹 LOAD IMAGES FOR A RECORD */
if (isset($_POST['load_images'])) {
    $doc_id = intval($_POST['doc_id']);

    $rows = mysqli_query($conn, "SELECT img_id, img_filename FROM tbl_document_images WHERE doc_id = $doc_id ORDER BY img_id DESC");

    $images = [];
    while ($r = mysqli_fetch_assoc($rows)) {
        $images[] = [
            'img_id' => (int)$r['img_id'],
            'url'    => '../uploads/' . $r['img_filename'],
        ];
    }

    echo json_encode(['images' => $images]);
    exit;
}


if(isset($_POST['load_rec_count'])){
    $select = "SELECT count(doc_id) as doc_count FROM `tbl_documents_registry`";
    $runselect = mysqli_query($conn, $select);
    if($runselect){
        $r = mysqli_fetch_assoc($runselect);
        echo $r['doc_count'];
    }
}

/* 🔹 GET SINGLE RECORD */
if (isset($_POST['get_record'])) {
  $id = $_POST['doc_id'];
  $sql = mysqli_query($conn, "SELECT * FROM tbl_documents_registry WHERE doc_id='$id'");
  $data = mysqli_fetch_assoc($sql);
  echo json_encode($data);
  exit;
}

/* 🔹 UPDATE RECORD */
if (isset($_POST['update_record'])) {
  $id = $_POST['doc_id'];
  $date_received = mysqli_real_escape_string($conn, $_POST['date_received']);
  $divisionid = mysqli_real_escape_string($conn, $_POST['divisionid']);
  $doctypeid = mysqli_real_escape_string($conn, $_POST['doctypeid']);
  $particular = mysqli_real_escape_string($conn, $_POST['particular']);

  $update = mysqli_query($conn, "
    UPDATE tbl_documents_registry 
    SET date_received='$date_received',
        office_division='$divisionid',
        type_of_documents='$doctypeid',
        particular='$particular'
    WHERE doc_id='$id'
  ");

  echo $update ? "updated" : "error";
  exit;
}

/* 🔹 GENERATE FILE CODE */
if (isset($_POST['generate_file_code'])) {
    mysqli_begin_transaction($conn);
    try {
        $getSeries = mysqli_query($conn, "SELECT * FROM tbl_file_series ORDER BY series_id DESC LIMIT 1 FOR UPDATE");
        $row = mysqli_fetch_assoc($getSeries);

        if ($row) {
            $prefix = $row['series_prefix'];
            $currentNum = $row['series_number'] + 1;
            $nextCode = $prefix . '-' . str_pad($currentNum, 5, "0", STR_PAD_LEFT);
            mysqli_query($conn, "UPDATE tbl_file_series SET series_number = $currentNum WHERE series_id = {$row['series_id']}");
        } else {
            $prefix = date('y');
            $currentNum = 1;
            $nextCode = $prefix . '-' . str_pad($currentNum, 5, "0", STR_PAD_LEFT);
            mysqli_query($conn, "INSERT INTO tbl_file_series (series_prefix, series_number) VALUES ('$prefix', $currentNum)");
        }

        mysqli_commit($conn);
        echo $nextCode;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "error";
    }
    exit;
}

/* 🔹 LOAD DROPDOWNS */
if (isset($_POST['load_dropdowns'])) {
    $divisions = "";
    $getdiv = mysqli_query($conn, "SELECT divisionid, division_desc FROM tbldivisions ORDER BY division_desc ASC");
    while ($d = mysqli_fetch_assoc($getdiv)) {
        $divisions .= "<option value='{$d['divisionid']}'>{$d['division_desc']}</option>";
    }

    $doctypes = "";
    $getdoc = mysqli_query($conn, "SELECT docid, doctype_desc FROM tbltypeofdocuments ORDER BY doctype_desc ASC");
    while ($t = mysqli_fetch_assoc($getdoc)) {
        $doctypes .= "<option value='{$t['docid']}'>{$t['doctype_desc']}</option>";
    }

    echo json_encode(['divisions' => $divisions, 'doctypes' => $doctypes]);
    exit;
}

/* 🔹 ADD RECORD */
if (isset($_POST['add_record'])) {
    $date_received = mysqli_real_escape_string($conn, $_POST['date_received']);
    $received_by   = $_SESSION['acc_id'];
    $file_code     = mysqli_real_escape_string($conn, $_POST['file_code']);
    $divisionid    = mysqli_real_escape_string($conn, $_POST['divisionid']);
    $doctypeid     = mysqli_real_escape_string($conn, $_POST['doctypeid']);
    $particular    = mysqli_real_escape_string($conn, strtoupper($_POST['particular']));
    $date_received_op = isset($_POST['date_received_op']) ? mysqli_real_escape_string($conn, $_POST['date_received_op']) : NULL;
    $action_taken  = isset($_POST['action_taken']) ? mysqli_real_escape_string($conn, $_POST['action_taken']) : NULL;

    $insert = mysqli_query($conn, "
        INSERT INTO tbl_documents_registry 
        (date_received, received_by, file_code, office_division, type_of_documents, particular)
        VALUES ('$date_received', '$received_by', '$file_code', '$divisionid', '$doctypeid', '$particular')
    ");

    echo $insert ? "success" : "error";
    exit;
}

/* 🔹 DELETE RECORD */
if (isset($_POST['delete_record'])) {
    $doc_id = mysqli_real_escape_string($conn, $_POST['doc_id']);
    $delete = mysqli_query($conn, "DELETE FROM tbl_documents_registry WHERE doc_id='$doc_id'");
    echo $delete ? "deleted" : "error";
    exit;
}

/* 🚀 SERVER-SIDE DATATABLES PROCESSING */
if (isset($_POST['server_table'])) {
    $columns = ['date_received', 'received_by', 'file_code', 'office_division', 'type_of_documents', 'particular', 'created_at'];

    $start = intval($_POST['start']);
    $length = intval($_POST['length']);
    $searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']);

    $where = "";
    if (!empty($searchValue)) {
        $where = "WHERE 
            d.file_code LIKE '%$searchValue%' OR 
            d.received_by LIKE '%$searchValue%' OR 
            v.division_desc LIKE '%$searchValue%' OR 
            t.doctype_desc LIKE '%$searchValue%' OR 
            d.particular LIKE '%$searchValue%'";
    }

    // Total count
    $totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_documents_registry");
    $totalData = mysqli_fetch_assoc($totalQuery)['total'];

    // Filtered count
    $queryFiltered = mysqli_query($conn, "
        SELECT COUNT(*) AS total 
        FROM tbl_documents_registry d
        LEFT JOIN tbldivisions v ON d.office_division = v.divisionid
        LEFT JOIN tbltypeofdocuments t ON d.type_of_documents = t.docid
        $where
    ");
    $totalFiltered = mysqli_fetch_assoc($queryFiltered)['total'];

    // Fetch paginated data
    $query = "
        SELECT d.doc_id, d.date_received, d.received_by, d.file_code, 
               v.division_desc AS office_division, 
               t.doctype_desc AS type_of_documents, 
               d.particular, d.created_at
        FROM tbl_documents_registry d
        LEFT JOIN tbldivisions v ON d.office_division = v.divisionid
        LEFT JOIN tbltypeofdocuments t ON d.type_of_documents = t.docid
        $where
        ORDER BY d.doc_id DESC
        LIMIT $start, $length
    ";
    $result = mysqli_query($conn, $query);

    $data = [];
    while ($r = mysqli_fetch_assoc($result)) {

    // 🕓 Format the datetime nicely
    if (!empty($r['date_received'])) {
        $r['date_received'] = strtoupper(date("M d, Y h:i A", strtotime($r['date_received'])));
    } else {
        $r['date_received'] = "";
    }

$r['actions'] = "
  <div class='d-grid gap-1' style='grid-template-columns: repeat(2, 1fr); display: grid;'>
    <button class='btn btn-info btn-sm' onclick='upload_image_record({$r['doc_id']})' title='Upload Image'>
      <i class='bx bx-image'></i>
    </button>
    <button class='btn btn-primary btn-sm' onclick='take_action({$r['doc_id']})' title='Take Action'>
      <i class='bx bx-cog'></i>
    </button>
    <button class='btn btn-warning btn-sm' onclick='edit_record({$r['doc_id']})' title='Edit Record'>
      <i class='bx bx-edit'></i>
    </button>
    <button class='btn btn-danger btn-sm' onclick='delete_record({$r['doc_id']})' title='Delete Record'>
      <i class='bx bx-trash'></i>
    </button>
  </div>
";
        $data[] = $r;
    }

    echo json_encode([
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $totalData,
        "recordsFiltered" => $totalFiltered,
        "data" => $data
    ]);
    exit;
}

/* 🔹 LOAD TABLE */
// if (isset($_POST['load_table'])) {
//     $output = '
//       <table id="requestTable" class="table table-hover table-sm align-middle">
//         <thead class="table-warning">
//           <tr>
//             <th>#</th>
//             <th>RECEIVED</th>
//             <th>CODE</th>
//             <th>DIVISION</th>
//             <th>TYPE</th>
//             <th>PARTICULAR</th>
//             <th class="text-center"></th>
//           </tr>
//         </thead>
//         <tbody>
//     ';

//     $sql = "
//         SELECT d.*, 
//                v.division_desc, 
//                t.doctype_desc
//         FROM tbl_documents_registry d
//         LEFT JOIN tbldivisions v ON d.office_division = v.divisionid
//         LEFT JOIN tbltypeofdocuments t ON d.type_of_documents = t.docid
//         ORDER BY d.doc_id DESC
//     ";
//     $run = mysqli_query($conn, $sql);
//     $count = 1;

//     while ($r = mysqli_fetch_assoc($run)) {
//         $output .= "
//           <tr>
//             <td class='text-end' width='1%'>{$count}.</td>
//             <td>{$r['date_received']}</td>
//             <td class='text-nowrap'>{$r['file_code']}</td>
//             <td>{$r['division_desc']}</td>
//             <td>{$r['doctype_desc']}</td>
//             <td>{$r['particular']}</td>
//             <td class='text-center text-nowrap' width='1%'>
//               <div class='btn-group btn-group-sm' role='group'>
//                 <button title='Take action' class='btn btn-primary' onclick='edit_record({$r['doc_id']})' title='Edit Record'>
//                   <i class='bx bx-cog'></i>
//                 </button>
//                 <button class='btn btn-warning' onclick='edit_record({$r['doc_id']})' title='Edit Record'>
//                   <i class='bx bx-edit'></i>
//                 </button>
//                 <button class='btn btn-danger' onclick='delete_record({$r['doc_id']})' title='Delete Record'>
//                   <i class='bx bx-trash'></i>
//                 </button>
//               </div>
//             </td>
//           </tr>
//         ";
//         $count++;
//     }

//     $output .= "</tbody></table>";
//     echo $output;
//     exit;
// }
?>
