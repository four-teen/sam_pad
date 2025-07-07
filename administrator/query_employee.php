<?php
session_start();
    ob_start();
    include '../db.php';

$get_month = "SELECT * FROM `tblsettings` WHERE acc_id='$_SESSION[acc_id]' LIMIT 1";
$runget_month = mysqli_query($conn, $get_month);
$rowget_month = mysqli_fetch_assoc($runget_month);
$curr_month = $rowget_month['set_month'];     // 1-12
$curr_year  = $rowget_month['set_year'];     // 4-digit year 


if (isset($_POST['get_settings'])) {
    $mnt = (int)$_POST['mnt'];
    $yr = (int)$_POST['yr'];

    if ($mnt <= 0 || $yr <= 0) {
        echo '<span class="text-danger">Settings required!</span>';
    } else {
        $readable_month = DateTime::createFromFormat('!m', $mnt)->format('F');
        echo $readable_month . ', ' . $yr;
    }
}


if(isset($_POST['save_settings'])){
    $set_month = $_POST['set_month'];
    $set_year = $_POST['set_year'];    

    $delete = "DELETE FROM `tblsettings` WHERE acc_id='$_SESSION[acc_id]'";
    $rundelete = mysqli_query($conn, $delete);

    $insert = "INSERT INTO `tblsettings` (`set_month`, `set_year`, `acc_id`) VALUES ('$set_month', '$set_year', '$_SESSION[acc_id]')";
    $runinsert = mysqli_query($conn, $insert);
}

if (isset($_POST['save_classed'])) {
    $emp_nums = $_POST['emp_nums'];
    $emp_classes = $_POST['emp_classes'];

    $insert = "INSERT INTO `tbl_set_class` (`fac_id`, `fac_class`) 
               VALUES ('$emp_nums', '$emp_classes') 
               ON DUPLICATE KEY UPDATE 
               `fac_class` = VALUES(`fac_class`)";

    $runinsert = mysqli_query($conn, $insert);
}

    if(isset($_POST['loading_employee'])){
        echo
        ''; ?>
                    <table id="payrollTable" class="table table-striped">
                      <thead>
                        <tr>
                          <th width="1%" style="white-space: nowrap;">#</th>
                          <th width="1%" class="text-nowrap">EMPLOYEE NUMBER</th>
                          <th width="1%"></th>
                          <th>EMPLOYEE NAME</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                            <?php 
                                $get_rec = "SELECT DISTINCT id_number, emp_no, name FROM `tbl_biometric_logs` 
                                WHERE 
                                accid='$_SESSION[acc_id]' AND 
                                curr_month = '$curr_month' AND 
                                curr_year='$curr_year'";
                                $runget_rec = mysqli_query($conn, $get_rec);
                                $count = 0;
                                while($row_rec = mysqli_fetch_assoc($runget_rec)){
                                    $emp_no = $row_rec['emp_no'];

                                    //CHECK EMPLOYEE CLASS
                                    $get_class = "SELECT * FROM `tbl_set_class` WHERE fac_id='$emp_no'";
                                    $runget_class = mysqli_query($conn, $get_class);
                                    $rowget_class = mysqli_fetch_assoc($runget_class);

                                    echo '
                                        <tr>
                                            <td class="align-middle">'.++$count.'</td>
                                            <td class="align-middle">'.$row_rec['id_number'].'</td>
                                            <td class="align-middle"><img src="blank.png" alt="" style="height: 18px; opacity: 0.2;"></td>                                           
                                            <td class="align-middle">'.$row_rec['name'].'</td>
                                            <td width="1%" class="text-nowrap">'.
                                              (is_null($rowget_class['fac_class']) ? '
                                                <div class="btn-group" role="group" aria-label="Basic mixed styles example" onclick="set_class(\''.$emp_no.'\')">
                                                    <button type="button" class="btn btn-success btn-sm"><i class="bx bxs-shower"></i></button>
                                                    <button type="button" class="btn btn-info btn-sm">Set Class</button>
                                                </div>
                                                ' :
                                                ($rowget_class['fac_class'] == 1 ? 
                                                    '
                                                <div class="btn-group" role="group" aria-label="Basic mixed styles example" onclick="set_class(\''.$emp_no.'\')">
                                                    <button type="button" class="btn btn-success btn-sm"><i class="bx bxs-shower"></i></button>
                                                    <button type="button" class="btn btn-warning btn-sm">'.($rowget_class['fac_class']==0?'Teaching':'Non-Teaching').'</button>
                                                </div>
                                                    ' : 
                                                    '
                                                <div class="btn-group" role="group" aria-label="Basic mixed styles example" onclick="set_class(\''.$emp_no.'\')">
                                                    <button type="button" class="btn btn-info btn-sm"><i class="bx bxs-shower"></i></button>
                                                    <button type="button" class="btn btn-success btn-sm">'.($rowget_class['fac_class']==0?'Teaching':'Non-Teaching').'</button>
                                                </div>
                                                    ')
                                              ).
                                            '</td>

                                            <td width="1%" class="text-nowrap">
                                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                                    <button type="button" class="btn btn-danger btn-sm"><i class="bx bxs-bookmarks" ></i></button>
                                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.open(\'employee_dtr.php?emp_no='.$emp_no.'&id_number='.$row_rec['id_number'].'\')">Print DTR</button>
                                                    <button type="button" class="btn btn-info btn-sm" onclick="window.open(\'employee_dtr_edit.php?emp_no='.$emp_no.'&id_number='.$row_rec['id_number'].'\')"><i class="bx bxs-edit-location"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    ';
                                }
                            ?>
                      </tbody>
                      
                    </table>
        <?php echo'';
    }

?>