<?php
ob_start();              // Optional but good for safety
session_start();         // Start session before anything else
include '../db.php';     // Then include database or other files


$office_id = $_SESSION['officeid'];

if(isset($_POST['delete_vehicle'])){
    $delete = "DELETE FROM tbl_vehicle WHERE vehicleid ='$_POST[id]'";
    $rundelete = mysqli_query($conn, $delete);
}


if(isset($_POST['saving_vehicle_records'])){
    $plate_number = addslashes($_POST['plate_number']);
    $vehicle = addslashes($_POST['vehicle']);  
    
    $insert = "INSERT INTO `tbl_vehicle` (`vehicle_info`, `vehicle_temp`) VALUES ('$vehicle', '$plate_number')";
    $runinsert = mysqli_query($conn, $insert);  

}

if (isset($_POST['get_vehicle_records'])) {

    $select = "SELECT * FROM `tbl_vehicle` ORDER BY vehicle_temp ASC";
    $runselect = mysqli_query($conn, $select);

    echo '<div class="row g-2">';

    while ($r = mysqli_fetch_assoc($runselect)) {

        echo '
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-left:4px solid #0d6efd;">
                <div class="card-body p-1">

                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 text-primary fw-bold">' . htmlspecialchars($r['vehicle_temp']) . '</h6>
                            <p class="mb-0 text-muted small">' . htmlspecialchars($r['vehicle_info']) . '</p>
                        </div>

                        <button class="btn btn-sm btn-danger" 
                                onclick="delete_vehicle(' . $r['vehicleid'] . ')">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>
        ';
    }

    echo '</div>';
}



if (isset($_POST['get_travels'])) {
    echo "loading list using cards for best mobile view implemented with infinitescroll";
}




?>
