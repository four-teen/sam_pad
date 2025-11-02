<?php
ob_start();
session_start();
include '../db.php';

$doc_id = intval($_POST['doc_id']);

$sql = "SELECT pres_action, pres_remarks, pres_acted_date 
        FROM tblpresident_actions 
        WHERE pres_doc_id = '$doc_id' 
        ORDER BY pres_acted_date DESC 
        LIMIT 1";

$res = mysqli_query($conn, $sql);

if (mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    echo json_encode([
        'status' => 'success',
        'action' => $row['pres_action'],
        'remarks' => $row['pres_remarks'],
        'date' => $row['pres_acted_date']
    ]);
} else {
    echo json_encode(['status' => 'none']);
}
?>
