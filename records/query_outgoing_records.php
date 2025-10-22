<?php
include '../db.php';
session_start();
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
               t.doctype_desc
        FROM tbl_documents_registry d
        LEFT JOIN tbldivisions v ON d.office_division = v.divisionid
        LEFT JOIN tbltypeofdocuments t ON d.type_of_documents = t.docid
        ORDER BY d.doc_id DESC
    ";
    $run = mysqli_query($conn, $sql);
    $count = 1;

    while ($r = mysqli_fetch_assoc($run)) {

        $check = "SELECT * FROM tbl_document_actions 
                  WHERE doc_id = '{$r['doc_id']}' 
                  AND from_office_id = '{$_SESSION['acc_id']}' 
                  ORDER BY action_id DESC
                  LIMIT 1";
        $runcheck = mysqli_query($conn, $check);
        $get_stat = '';

        // ✅ get latest action type
        while($rowcheck = mysqli_fetch_assoc($runcheck)){
            $get_stat = $rowcheck['action_type'];
        }

        if (mysqli_num_rows($runcheck) >= 1) {
            $output .= '
              <tr>
                <td class="text-end" width="1%">'.$count.'.</td>
                <td>'.$r['date_received'].'</td>
                <td class="text-nowrap">'.$r['file_code'].'</td>
                <td>'.$r['division_desc'].'</td>
                <td>'.$r['doctype_desc'].'</td>
                <td>'.$r['particular'].'</td>
                <td class="text-center text-nowrap" width="1%">';
            
            // ✅ Move button logic inside the same echo flow
            if($get_stat === 'Outgoing'){
                $output .= '
                  <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-warning" title="Outgoing">
                      <i class="bx bxs-right-arrow"></i>
                    </button>
                    <button class="btn btn-warning bg-white">'.$get_stat.'</button>
                  </div>
                ';
            } elseif($get_stat === 'Received'){
                $output .= '
                  <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-success" title="Received">
                      <i class="bx bxs-up-arrow"></i>
                    </button>
                    <button class="btn btn-success bg-white text-danger">'.$get_stat.'</button>
                  </div>
                ';
            }

            $output .= '</td></tr>';
        }

        $count++;
    }

    $output .= "</tbody></table>";
    echo $output;
    exit;
}


?>
