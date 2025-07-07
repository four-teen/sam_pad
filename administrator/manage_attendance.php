<?php
session_start();
ob_start();
require '../db.php'; // your DB connection
require '../vendor/autoload.php'; // PhpSpreadsheet


//get month settings
$get_month = "SELECT * FROM `tblsettings` WHERE acc_id='$_SESSION[acc_id]' LIMIT 1";
$runget_month = mysqli_query($conn, $get_month);
$rowget_month = mysqli_fetch_assoc($runget_month);
$curr_month = $rowget_month['set_month'];     // 1-12
$curr_year  = $rowget_month['set_year'];     // 4-digit year 

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['excel_file'])) {
    // sleep(5);
    $file = $_FILES['excel_file']['tmp_name'];
    $filename = $_FILES['excel_file']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
        http_response_code(400);
        echo "Invalid file type.";
        exit;
    }

function parse_biometric_datetime($raw) {
    $raw = trim(preg_replace('/\s+/', ' ', $raw));
    $raw = str_ireplace(['AM', 'PM'], ['am', 'pm'], $raw);

    $formats = [
        'd/m/Y g:i a',   // 26/06/2025 6:33 am
        'd/m/Y h:i a',   // 26/06/2025 06:33 am
        'd/m/Y H:i',     // 26/06/2025 18:33
        'm/d/Y g:i a',   // 06/26/2025 6:33 am
        'Y-m-d H:i:s'    // 2025-06-26 06:33:00
    ];

    foreach ($formats as $format) {
        $dt = DateTime::createFromFormat($format, $raw);
        if ($dt && $dt->getLastErrors()['error_count'] == 0) {
            return $dt->format('Y-m-d H:i:s');
        }
    }

    // Excel numeric serial fallback
    if (is_numeric($raw)) {
        $base = DateTime::createFromFormat('Y-m-d', '1899-12-30');
        $dt = clone $base;
        $dt->modify("+$raw days");
        return $dt->format('Y-m-d H:i:s');
    }

    return null;
}

// Your try block:
try {
    if ($ext === 'csv') {

        $delete = "DELETE FROM tbl_biometric_logs WHERE accid='$_SESSION[acc_id]' AND curr_month = '$curr_month' AND curr_year='$curr_year'";
        $rundelete = mysqli_query($conn, $delete);

        if (($handle = fopen($file, "r")) !== false) {
            $header = fgetcsv($handle); // skip header
            while (($row = fgetcsv($handle)) !== false) {
                $department   = $row[0] ?? '';
                $name         = $row[1] ?? '';
                $emp_no       = $row[2] ?? '';
                $datetime_raw = $row[3] ?? '';
                $status       = $row[4] ?? '';
                $location_id  = $row[5] ?? '';
                $id_number    = $row[6] ?? '';
                $verify_code  = $row[7] ?? '';
                $card_no      = $row[8] ?? '';

                // Parse and format datetime
                $datetime = parse_biometric_datetime($datetime_raw);

                if (!$datetime) {
                    error_log("❌ Invalid datetime: [$datetime_raw]");
                    continue; // skip invalid
                }
                $acc_id     = $_SESSION['acc_id'];
                $currs_month = $curr_month;     // 1-12
                $currs_year  = $curr_year;     // 4-digit year 

                $stmt = $conn->prepare("INSERT INTO tbl_biometric_logs 
                    (department, name, emp_no, datetime, status, location_id, id_number, verify_code, card_no, accid, curr_month, curr_year) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "ssisssssssis",  // 12 placeholders
                    $department,     // s
                    $name,           // s
                    $emp_no,         // i
                    $datetime,       // s
                    $status,         // s
                    $location_id,    // s
                    $id_number,      // s
                    $verify_code,    // s
                    $card_no,        // s
                    $acc_id,         // i
                    $currs_month,     // i
                    $currs_year       // s
                );
                $stmt->execute();

            }
            fclose($handle);
        }
    }

    echo "success";

} catch (Exception $e) {
    echo "<script>alert('Error processing the file: " . $e->getMessage() . "');</script>";
}

}
?>
