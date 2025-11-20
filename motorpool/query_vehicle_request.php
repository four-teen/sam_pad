<?php 
ob_start();              // Optional but good for safety
session_start();         // Start session before anything else
include '../db.php';     // Then include database or other files


if (isset($_POST['load_request_list'])) {

    $sql = "SELECT r.*, v.vehicle_temp, d.fullname AS driver_name
            FROM tbl_vehicle_request r
            LEFT JOIN tbl_vehicle v ON r.vehicleid = v.vehicleid
            LEFT JOIN tbl_driver d ON r.driverid = d.driverid
            ORDER BY r.requestid DESC";

    $run = mysqli_query($conn, $sql);

while ($r = mysqli_fetch_assoc($run)) {

    $going_date  = strtoupper(date("M d, Y", strtotime($r['date_from'])));
    $return_date = strtoupper(date("M d, Y", strtotime($r['date_to'])));

    echo "
    <div class='card mb-3 request-card'>
      <div class='card-body'>

        <h5 class='fw-bold mb-1'>{$r['vehicle_temp']}</h5>

        <div class='mb-2'>
          <small class='text-muted d-block'>Driver: {$r['driver_name']}</small>
          <small class='text-muted d-block'>Requisitioner: {$r['requisitioner']}</small>
        </div>

        <div class='mb-2'>
          <div class='fw-bold text-primary small mb-1 d-flex align-items-center'>
            <i class='bi bi-geo-alt-fill me-1'></i>
            Going: {$going_date}
          </div>

          <div class='fw-bold text-success small d-flex align-items-center'>
            <i class='bi bi-flag-fill me-1'></i>
            Return: {$return_date}
          </div>
        </div>

        <div class='text-muted small mb-3'>
          Purpose: {$r['purpose']}
        </div>

        <div class='d-flex justify-content-end gap-2'>
          <button class='btn btn-sm btn-warning' onclick='edit_request({$r['requestid']})'>
            <i class='bi bi-pencil-square'></i> Edit
          </button>

          <button class='btn btn-sm btn-danger' onclick='delete_request({$r['requestid']})'>
            <i class='bi bi-trash'></i> Delete
          </button>
        </div>

      </div>
    </div>
    ";
}

}


if (isset($_POST['save_request'])) {

    $id = $_POST['requestid'];
    $daterequest = $_POST['daterequest'];
    $veh = $_POST['plateNumber'];
    $driver = $_POST['driver'];
    $fullname = $_POST['fullname'];
    $dateFrom = $_POST['dateFrom'];
    $dateTo = $_POST['dateTo'];
    $purpose = $_POST['purpose'];
    $numPass = $_POST['numPass'];
    $listPass = $_POST['listPass'];
    $departure = $_POST['departure'];
    $meetingPlace = $_POST['meetingPlace'];

    if ($id == "") {
        // INSERT
        $sql = "INSERT INTO tbl_vehicle_request 
        (daterequest, vehicleid, driverid, requisitioner, date_from, date_to, purpose, num_pass, list_passenger, departure_time, meeting_place, status, created_at)
        VALUES ('$daterequest','$veh','$driver','$fullname','$dateFrom','$dateTo','$purpose','$numPass','$listPass','$departure','$meetingPlace','Going',NOW())";
    } else {
        // UPDATE
        $sql = "UPDATE tbl_vehicle_request SET
        daterequest='$daterequest',
        vehicleid='$veh',
        driverid='$driver',
        requisitioner='$fullname',
        date_from='$dateFrom',
        date_to='$dateTo',
        purpose='$purpose',
        num_pass='$numPass',
        list_passenger='$listPass',
        departure_time='$departure',
        meeting_place='$meetingPlace'
        WHERE requestid='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo mysqli_error($conn);
    }
}



 ?>