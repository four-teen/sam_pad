<?php
include '../db.php';
ob_start();
session_start();


// ==========================================


//RETURN RECORDS=======================
if (isset($_POST['return_document_action'])) {
    $doc_id = intval($_POST['doc_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $current_user = $_SESSION['fullname'] ?? 'Unknown User';

    $get = "SELECT * FROM tbl_document_actions 
            WHERE doc_id='$doc_id' 
            ORDER BY action_id DESC LIMIT 1";
    $run = mysqli_query($conn, $get);

    if (mysqli_num_rows($run) > 0) {
        $row = mysqli_fetch_assoc($run);

        $from_office = $row['to_office_id'];  // current receiver
        $to_office   = $row['from_office_id']; // previous sender

        $remarks = "Document returned by $current_user. Reason: $reason";

        $insert = "INSERT INTO tbl_document_actions 
                    (doc_id, from_office_id, to_office_id, action_type, action_remarks, action_date)
                    VALUES ('$doc_id', '$from_office', '$to_office', 'Returned', '$remarks', NOW())";

        $runinsert = mysqli_query($conn, $insert);

        echo $runinsert ? "success" : "failed";
    } else {
        echo "no_record_found";
    }

    exit;
}



//END OF RETURN RECORDS===============



if (isset($_POST['load_images_for_view'])) {
    $doc_id = intval($_POST['doc_id']);

    // Get document info
    $doc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_code, particular FROM tbl_documents_registry WHERE doc_id='$doc_id'"));

    // Get uploaded images
    $imgs = [];
    $qimgs = mysqli_query($conn, "SELECT * FROM tbl_document_images WHERE doc_id='$doc_id'");
    while ($r = mysqli_fetch_assoc($qimgs)) {
        $imgs[] = [
            'img_id' => $r['img_id'],
            'url' => '../uploads/' . $r['img_filename']
        ];
    }

    echo json_encode([
        'file_code' => $doc['file_code'] ?? '',
        'particular' => $doc['particular'] ?? '',
        'images' => $imgs
    ]);
    exit;
}


// if(isset($_POST['take_action_received'])){

//   $doc_id = $_POST['doc_id'];
//   $received_by = $_POST['received_by'];
//   $office_division = $_POST['office_division'];

//   $insert = "INSERT INTO `tbl_document_actions` (`doc_id`, `from_office_id`, `to_office_id`, `action_type`, `action_remarks`, `action_date`) VALUES ('$doc_id', '$received_by', '1', 'Received', '', current_timestamp())";
//   $runinsert = mysqli_query($conn, $insert);
// }

/* ✅ When PAD confirms document receipt */
if (isset($_POST['take_action_received'])) {
    $doc_id = intval($_POST['doc_id']);
    $received_by = mysqli_real_escape_string($conn, $_POST['received_by']);
    $office_division = mysqli_real_escape_string($conn, $_POST['office_division']);
    $receiver_name = $_SESSION['fullname'] ?? 'Unknown Receiver';

    // 🟢 Get the latest outgoing record
    $check = "SELECT * FROM tbl_document_actions 
              WHERE doc_id = '$doc_id' 
              ORDER BY action_id DESC 
              LIMIT 1";
    $runcheck = mysqli_query($conn, $check);
    $rowcheck = mysqli_fetch_assoc($runcheck);

    $from_office = $rowcheck['from_office_id'];
    $to_office   = $rowcheck['to_office_id'];

    // 🕒 Use PHP’s Manila timezone to insert accurate local time
    $current_datetime = date('Y-m-d H:i:s');

    $insert = "INSERT INTO tbl_document_actions 
                (doc_id, from_office_id, to_office_id, action_type, action_remarks, action_date)
               VALUES 
                ('$doc_id', '$from_office', '$to_office', 
                 'Received', 'Received by $receiver_name.', '$current_datetime')";
    $runinsert = mysqli_query($conn, $insert);

    echo $runinsert ? "success" : "failed";
    exit;
}





/* 🔹 LOAD TABLE */
if (isset($_POST['load_table'])) {
    $output = '
      <table id="outgoingTable" class="table table-sm table-hover table-sm align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>RECEIVED</th>
            <th>CODE</th>
            <th>DIVISION</th>
            <th>TYPE</th>
            <th>PARTICULAR</th>
            <th class="text-center">STATUS</th>
          </tr>
        </thead>
        <tbody>
    ';

    $sql = "
        SELECT d.*, 
           v.division_desc, 
           t.doctype_desc,
           a.to_office_id,
           a.action_id,
           a.action_type
    FROM tbl_documents_registry d
    LEFT JOIN tbldivisions v ON d.office_division = v.divisionid
    LEFT JOIN tbltypeofdocuments t ON d.type_of_documents = t.docid
    INNER JOIN (
        SELECT doc_id, MAX(action_id) AS latest_action
        FROM tbl_document_actions
        GROUP BY doc_id
    ) latest ON d.doc_id = latest.doc_id
    INNER JOIN tbl_document_actions a ON a.action_id = latest.latest_action
    WHERE a.action_type = 'Outgoing'
      AND a.to_office_id = '68'
    ORDER BY d.doc_id DESC
    ";
    $run = mysqli_query($conn, $sql);
    $count = 1;

    while ($r = mysqli_fetch_assoc($run)) {

        $check = "SELECT doc_id, MAX(action_id) AS latest_action
        FROM tbl_document_actions
        GROUP BY doc_id";
        $runcheck = mysqli_query($conn, $check);
        $get_stat = '';

        if (mysqli_num_rows($runcheck) >= 1) {
            $output .= '
              <tr>
                <td class="text-end" width="1%">'.$count.'.</td>
                <td>'.$r['date_received'].'</td>
                <td class="text-nowrap">'.$r['file_code'].'</td>
                <td>'.$r['division_desc'].'</td>
                <td>'.$r['doctype_desc'].'</td>
                <td>'.$r['particular'].'</td>
                <td class="text-nowrap" width="1%">';

                $output .= '               
                    <button class="btn btn-info" title="View Images" onclick="view_uploaded_images(\''.$r['doc_id'].'\')">
                      <i class="bi bi-images"></i>
                    </button> 
                    <button  onclick="confirmDocumentReceipt(\''.$r['doc_id'].'\',\''.$r['received_by'].'\',\''.$r['office_division'].'\')" class="btn btn-success" title="Recieved this document">
                      <i class="bi bi-arrow-90deg-right"></i>
                    </button>

                ';
          

            $output .= '</td></tr>';
        }

        $count++;
    }

    $output .= "</tbody></table>";
    echo $output;
    exit;
}


?>
