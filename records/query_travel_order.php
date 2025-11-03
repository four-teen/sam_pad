<?php
ob_start();
session_start();
include '../db.php';


/* 🔹 Load list of campuses */
if (isset($_POST['load_campus'])) {
    $data = [];
    $sql = "SELECT campusid, campusname FROM tblcampus ORDER BY campusname ASC";
    $res = mysqli_query($conn, $sql);
    while ($r = mysqli_fetch_assoc($res)) {
        $data[] = $r;
    }
    echo json_encode($data);
    exit;
}

/* 🔹 Load faculty list for Select2 */
if (isset($_POST['search_faculty'])) {
    $query = mysqli_real_escape_string($conn, $_POST['query'] ?? '');
    $data = [];
    $sql = "SELECT acc_id, acc_name 
            FROM tblprofiles 
            WHERE acc_name LIKE '%$query%'
            ORDER BY acc_name ASC 
            LIMIT 20";
    $res = mysqli_query($conn, $sql);
    while ($r = mysqli_fetch_assoc($res)) {
        $data[] = $r;
    }
    echo json_encode($data);
    exit;
}

/* 🔹 Save travel order */
if (isset($_POST['save_travel_order'])) {
    $doc_id = intval($_POST['doc_id']);
    $faculty_ids = $_POST['faculty_ids'] ?? [];
    $campusid = intval($_POST['campus']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    $date = $_POST['date'];
    $departure = $_POST['departure_date'];
    $return = $_POST['return_date'];
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $vehicle = mysqli_real_escape_string($conn, $_POST['vehicle']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    mysqli_begin_transaction($conn);
    try {
        $insert = "INSERT INTO tbl_travel_order 
            (doc_id, to_designation, to_station, to_destination, to_purpose, to_date, 
             to_departure_date, to_return_date, to_type, to_vehicle, to_remarks)
            VALUES 
            ('$doc_id', '$designation', '$campusid', '$destination', '$purpose', 
             '$date', '$departure', '$return', '$type', '$vehicle', '$remarks')";
        mysqli_query($conn, $insert);
        $to_id = mysqli_insert_id($conn);

        foreach ($faculty_ids as $fid) {
            $fid = intval($fid);
            mysqli_query($conn, "INSERT INTO tbl_travel_order_faculty (to_id, acc_id) VALUES ('$to_id', '$fid')");
        }

        mysqli_commit($conn);
        echo "success";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "error: " . $e->getMessage();
    }
    exit;
}


?>
