<?php
include '../db.php';
session_start();


// ==========================================

if (isset($_POST['load_offices'])) {
    $output = '';
    $get_offices = "SELECT office_id, office_name FROM tbl_office_heads ORDER BY office_name ASC";
    $run_offices = mysqli_query($conn, $get_offices);

    while ($r = mysqli_fetch_assoc($run_offices)) {
        $output .= '<option value="'.$r['office_id'].'">'.$r['office_name'].'</option>';
    }

    echo $output;
    exit;
}

if (isset($_POST['send_back_with_selection'])) {
    $doc_id = intval($_POST['doc_id']);
    $from_office_id = intval($_POST['from_office_id']);
    $to_office_id = intval($_POST['to_office_id']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    // Safety check — none should be zero
    if ($doc_id > 0 && $from_office_id > 0 && $to_office_id > 0) {
        $insert = "
            INSERT INTO tbl_document_actions 
            (doc_id, from_office_id, to_office_id, action_type, action_remarks, action_date)
            VALUES ('$doc_id', '$from_office_id', '$to_office_id', 'Acted', '$remarks', NOW())
        ";

        if (mysqli_query($conn, $insert)) {
            echo 'success';
        } else {
            echo 'db_error: ' . mysqli_error($conn);
        }
    } else {
        echo 'missing_fields';
    }
    exit;
}



/* 🔹 LOAD TABLE (RECEIVED) */
if (isset($_POST['load_table_received'])) {
    $output = '
      <table id="receivedTable" class="table table-sm table-hover align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>RECEIVED</th>
            <th>LAPSED</th>
            <th>CODE</th>
            <th>DIVISION</th>
            <th>TYPE</th>
            <th>PARTICULAR</th>
            <th class="text-center">ACTIONS</th>
          </tr>
        </thead>
        <tbody>
    ';

    // ✅ Get only documents whose latest action is "Received"
    $sql = "
        SELECT d.*, 
               v.division_desc, 
               t.doctype_desc,
               a.to_office_id,
               a.action_type,
               a.action_date
        FROM tbl_documents_registry d
        LEFT JOIN tbldivisions v ON d.office_division = v.divisionid
        LEFT JOIN tbltypeofdocuments t ON d.type_of_documents = t.docid
        INNER JOIN (
            SELECT doc_id, MAX(action_id) AS latest_action
            FROM tbl_document_actions
            GROUP BY doc_id
        ) x ON d.doc_id = x.doc_id
        INNER JOIN tbl_document_actions a ON a.action_id = x.latest_action
        WHERE a.action_type = 'Received'
          AND a.to_office_id = '68'
        ORDER BY d.doc_id DESC
    ";

    $run = mysqli_query($conn, $sql);
    $count = 1;

    while ($r = mysqli_fetch_assoc($run)) {

        date_default_timezone_set('Asia/Manila');
        // ✅ Compute days/hours since received
        $receivedDate = new DateTime($r['action_date']);
        $currentDate = new DateTime();
        $interval = $receivedDate->diff($currentDate);

        if ($interval->days >= 1) {
            $daysLapsed = $interval->days . ' day' . ($interval->days > 1 ? 's' : '') . ' ago';
        } else {
            $hours = $interval->h;
            $minutes = $interval->i;
            if ($hours >= 1) {
                $daysLapsed = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
            } elseif ($minutes >= 1) {
                $daysLapsed = $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
            } else {
                $daysLapsed = 'Just now';
            }
        }

        // ✅ Format table row
        $output .= '
          <tr>
            <td class="text-end" width="1%">'.$count.'.</td>
            <td>'.date("Y-m-d h:i:s", strtotime($r['action_date'])).'</td>
            <td>'.$daysLapsed.'</td>
            <td class="text-nowrap">'.$r['file_code'].'</td>
            <td>'.$r['division_desc'].'</td>
            <td>'.$r['doctype_desc'].'</td>
            <td>'.$r['particular'].'</td>
            <td class="text-nowrap text-center" width="1%">
              <div style="
                  display: grid; 
                  grid-template-columns: repeat(2, 1fr); 
                  gap: 4px; 
                  justify-items: center;
              ">            
                <button class="btn btn-info btn-sm" title="View Images" onclick="view_uploaded_images(\''.$r['doc_id'].'\')">
                    <i class="bi bi-images"></i>
                </button>
                <button onclick="confirmReturnDocument(\''.$r['doc_id'].'\')" class="btn btn-danger btn-sm" title="Return">
                    <i class="bi bi-bootstrap-reboot"></i>
                </button>
                <button 
                  class="btn btn-primary btn-sm forward-records"
                  data-docid='.$r['doc_id'].'
                  data-from='.$_SESSION['officeid'].'
                  title="Forward to records">
                  <i class="bi bi-fast-forward-circle"></i>
                </button>
              </div>
            </td>
          </tr>
        ';
        $count++;
    }

    $output .= "</tbody></table>";
    echo $output;
    exit;
}




?>
