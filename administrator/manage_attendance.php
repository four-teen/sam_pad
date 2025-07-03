<?php
ob_start();
require 'db.php'; // your DB connection
require 'vendor/autoload.php'; // PhpSpreadsheet

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

        $delete = "DELETE FROM tbl_biometric_logs";
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

                $stmt = $conn->prepare("INSERT INTO tbl_biometric_logs 
                    (department, name, emp_no, datetime, status, location_id, id_number, verify_code, card_no) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssissssss", $department, $name, $emp_no, $datetime, $status, $location_id, $id_number, $verify_code, $card_no);
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
