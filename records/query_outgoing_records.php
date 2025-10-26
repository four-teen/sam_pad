<?php
include '../db.php';
session_start();


/* 🔹 Get return remarks for a document */
if (isset($_POST['get_return_remarks'])) {
    $doc_id = intval($_POST['doc_id']);
    $sql = "SELECT action_remarks FROM tbl_document_actions
            WHERE doc_id='$doc_id' AND action_type='Returned'
            ORDER BY action_id DESC LIMIT 1";
    $run = mysqli_query($conn, $sql);

    if ($r = mysqli_fetch_assoc($run)) {
        echo json_encode([
            "status" => "success",
            "remarks" => $r['action_remarks']
        ]);
    } else {
        echo json_encode(["status" => "no_data"]);
    }
    exit;
}


/* 🔹 Re-send returned document */
if (isset($_POST['resend_returned_doc'])) {
    $doc_id = intval($_POST['doc_id']);
    $sender_id = $_SESSION['acc_id'] ?? 0;
    $sender_name = $_SESSION['fullname'] ?? 'Unknown User';

    // Get the latest returned record to retrieve routing info
    $get = "SELECT * FROM tbl_document_actions 
            WHERE doc_id='$doc_id' 
            AND action_type='Returned'
            ORDER BY action_id DESC LIMIT 1";
    $run = mysqli_query($conn, $get);

    if ($row = mysqli_fetch_assoc($run)) {
        $from_office = $row['to_office_id']; // previous sender
        $to_office   = $row['from_office_id']; // PAD

        $remarks = "Re-sent by $sender_name after corrections.";

        $insert = "INSERT INTO tbl_document_actions
                    (doc_id, from_office_id, to_office_id, action_type, action_remarks, action_date)
                   VALUES
                    ('$doc_id', '$from_office', '$to_office', 'Outgoing', '$remarks', NOW())";
        $runinsert = mysqli_query($conn, $insert);

        echo $runinsert ? "success" : "failed";
    } else {
        echo "no_record_found";
    }

    exit;
}


/* 🔹 LOAD TABLE */
if (isset($_POST['load_table'])) {
    $output = '
      <table id="outgoingTable" class="table table-sm table-hover align-middle">
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

    // ✅ Fetch all documents created by the current Records office
    $sql = "
        SELECT d.*, 
               v.division_desc, 
               t.doctype_desc
        FROM tbl_documents_registry d
        LEFT JOIN tbldivisions v ON d.office_division = v.divisionid
        LEFT JOIN tbltypeofdocuments t ON d.type_of_documents = t.docid
        ORDER BY d.doc_id DESC
    ";
    $run = mysqli_query($conn, $sql);
    $count = 1;

    while ($r = mysqli_fetch_assoc($run)) {

        // ✅ Find the latest global action for this document
        $check = "
            SELECT action_type, from_office_id, to_office_id
            FROM tbl_document_actions
            WHERE doc_id = '{$r['doc_id']}'
            ORDER BY action_id DESC
            LIMIT 1
        ";
        $runcheck = mysqli_query($conn, $check);
        $get_stat = '';
        $from_office = '';
        $to_office = '';

        if ($rowcheck = mysqli_fetch_assoc($runcheck)) {
            $get_stat = $rowcheck['action_type'];
            $from_office = $rowcheck['from_office_id'];
            $to_office = $rowcheck['to_office_id'];
        }

        // ✅ Only display records originally sent by this office
        if ($from_office == $_SESSION['acc_id'] || $to_office == $_SESSION['acc_id']) {
            $output .= '
              <tr>
                <td class="text-end" width="1%">'.$count.'.</td>
                <td>'.$r['date_received'].'</td>
                <td class="text-nowrap">'.$r['file_code'].'</td>
                <td>'.$r['division_desc'].'</td>
                <td>'.$r['doctype_desc'].'</td>
                <td>'.$r['particular'].'</td>
                <td class="text-center text-nowrap" width="1%">';

            // ✅ Status display logic
            if ($get_stat === 'Outgoing') {
                $output .= '
                    <div class="btn-group btn-group-sm" role="group">
                      <button class="btn btn-warning" title="Outgoing">
                        <i class="bx bxs-right-arrow"></i>
                      </button>
                      <button class="btn btn-warning bg-white">'.$get_stat.'</button>
                    </div>
                ';
            } elseif ($get_stat === 'Received') {
                $output .= '
                    <div class="btn-group btn-group-sm" role="group">
                      <button class="btn btn-success" title="Received">
                        <i class="bx bxs-check-circle"></i>
                      </button>
                      <button class="btn btn-success bg-white text-danger">'.$get_stat.'</button>
                    </div>
                ';
            } elseif ($get_stat === 'Returned') {
    $output .= '
        <div class="btn-group btn-group-sm" role="group">
          <button class="btn btn-danger" 
                  title="Returned" 
                  onclick="viewReturnRemarks(\''.$r['doc_id'].'\')">
            <i class="bx bxs-error"></i>
          </button>
          <button class="btn btn-danger bg-white text-danger" 
                  onclick="viewReturnRemarks(\''.$r['doc_id'].'\')">
            '.$get_stat.'
          </button>
        </div>
    ';
            }

            $output .= '</td></tr>';
            $count++;
        }
    }

    $output .= "</tbody></table>";
    echo $output;
    exit;
}
?>
