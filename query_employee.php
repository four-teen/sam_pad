<?php
    ob_start();
    include 'db.php';


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
                          <th>ID</th>
                          <th>EMPLOYEE NAME</th>
                          <th>CLASS</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                            <?php 
                                $get_rec = "SELECT DISTINCT id_number, emp_no, name FROM `tbl_biometric_logs`";
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
                                            <td>'.++$count.'</td>
                                            <td>'.$row_rec['id_number'].'</td>
                                            <td>'.$row_rec['name'].'</td>
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