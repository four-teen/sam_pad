<?php 
ob_start();              // Optional but good for safety
session_start();         // Start session before anything else
include '../db.php';     // Then include database or other files

if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
    header('location:../logout.php');
    exit;
}



if(isset($_POST['load_rec_count'])){
    $check = "SELECT  count(*) as all_docs
                FROM `tbl_documents_registry`
                WHERE uni_divisionid='$_SESSION[officeid]'";
    $runcheck = mysqli_query($conn, $check);
    if($runcheck){
        $r = mysqli_fetch_assoc($runcheck);
        echo $r['all_docs'];
    }
}



//received documents
if(isset($_POST['get_outgoing_counter'])){
    $check = "SELECT  count(tbl_documents_registry.doc_id) as received_counter
                FROM `tbl_document_actions`
                INNER JOIN tbl_documents_registry ON tbl_documents_registry.doc_id=tbl_document_actions.doc_id
                WHERE action_type='Received' AND to_office_id='$_SESSION[officeid]'";
    $runcheck = mysqli_query($conn, $check);
    if($runcheck){
        $r = mysqli_fetch_assoc($runcheck);
        echo $r['received_counter'];
    }
}


//Delivered records
if (isset($_POST['get_received_counter'])) {

    $office_id = $_SESSION['officeid'];

    // Count documents for this office that do NOT have a 'Received' action
    $sql = "
        SELECT COUNT(*) AS vpaa_records_received
        FROM tbl_documents_registry d
        WHERE d.uni_divisionid = '$office_id'
          AND NOT EXISTS (
                SELECT 1
                FROM tbl_document_actions a
                WHERE a.doc_id = d.doc_id
                  AND a.action_type = 'Received'
          )
    ";

    $run = mysqli_query($conn, $sql);

    if ($run) {
        $row = mysqli_fetch_assoc($run);
        echo $row['vpaa_records_received'];
    } else {
        error_log('SQL Error: ' . mysqli_error($conn));
        echo 0;
    }
}


?>