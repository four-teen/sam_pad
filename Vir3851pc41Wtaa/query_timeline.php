<?php
ob_start();              // Optional but good for safety
session_start();         // Start session before anything else
include '../db.php';     // Then include database or other files

if (!isset($_SESSION['username']) || $_SESSION['username'] == '') {
    header('location:../logout.php');
    exit;
}

// ===============START===================================


if (isset($_POST['loading_records'])) {
   echo 
   ''; ?>
    <div class="table-responsive">
      <table id="docTable" class="table table-hover table-sm">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>CODE</th>
            <th>PARICULAR</th>
            <th></th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
            <?php 
                $sql = "SELECT  *
                FROM `tbl_documents_registry`
                WHERE uni_divisionid='$_SESSION[officeid]'";
                $run = mysqli_query($conn, $sql);
                $count = 1;
                while ($r = mysqli_fetch_assoc($run)) {
                    echo
                    '
                      <tr>
                        <td width="1%" class="text-end">'.$count++.'.</td>
                        <td class="text-nowrap">'.$r['file_code'].'</td>
                        <td>'.$r['particular'].'</td>
                        <td></td>

                        <td width="1%" class="text-center text-nowrap">                       
                          <button class="btn btn-info btn-sm" onclick="get_timeline('.$r['doc_id'].')">
                            <i class="bi bi-stopwatch-fill"></i>
                          </button>

                        </td>
                      </tr>
                    ';
                }

            ?>
        </tbody>
      </div>
   <?php echo'';


}


?>