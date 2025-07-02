<?php 
ob_start();
include 'db.php';

$emp_no = $_GET['emp_no'];
$select = "SELECT * FROM `tbl_biometric_logs` WHERE emp_no='$emp_no' ORDER BY datetime ASC";
$runselect = mysqli_query($conn, $select);

// ⚠️ Don't fetch the first row here!
$rowselect = null; // We'll handle this manually

$structured_logs = [];

// Fetch all rows first
$all_rows = [];
while ($row = mysqli_fetch_assoc($runselect)) {
    // Save first row for header
    if (!$rowselect) {
        $rowselect = $row;
    }
    $all_rows[] = $row;
}

// Now process all rows
foreach ($all_rows as $log) {
    $datetime = new DateTime($log['datetime']);
    $day = (int)$datetime->format('j'); // 1 to 31
    $time = $datetime->format('H:i');
    $hour = (int)$datetime->format('H');
    $minute = (int)$datetime->format('i');
    $status = $log['status']; // C/In or C/Out

    if (!isset($structured_logs[$day])) {
        $structured_logs[$day] = [
            'am_in' => '',
            'am_out' => '',
            'pm_in' => '',
            'pm_out' => '',
        ];
    }

    if (strtolower($status) == 'c/in') {
        if ($hour < 12) {
            if ($structured_logs[$day]['am_in'] == '') {
                $structured_logs[$day]['am_in'] = $time;
            }
        } else {
            if ($structured_logs[$day]['pm_in'] == '') {
                $structured_logs[$day]['pm_in'] = $time;
            }
        }
    } elseif (strtolower($status) == 'c/out') {
        if (($hour < 12 || ($hour == 12 && $structured_logs[$day]['am_out'] == '' && $structured_logs[$day]['pm_in'] == ''))) {
            if ($structured_logs[$day]['am_out'] == '') {
                $structured_logs[$day]['am_out'] = $time;
            }
        } else {
            if ($structured_logs[$day]['pm_out'] == '') {
                $structured_logs[$day]['pm_out'] = $time;
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee DTR</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #ccc;
    }

    .legal-page {
      width: 8.5in;
      height: 13in;
      background: white;
      margin: 20px auto;
      padding: 20px 30px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    .dtr-section {
      width: 48%;
      display: inline-block;
      vertical-align: top;
    }

    .dtr-section .info-line {
      margin-bottom: 6px;
      font-size: 14px;
    }

    .dtr-section table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }

    .dtr-section th, .dtr-section td {
      border: 1px solid #000;
      text-align: center;
      padding: 3px;
    }

    .dtr-section th {
      background: #f0f0f0;
    }

    .title-label {
      font-weight: bold;
      font-size: 16px;
      text-align: left;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    .info-line {
      font-size: 14px;
      margin: 3px 0;
    }

    hr {
      border: 1px solid #000;
      margin: 10px 0;
    }

    .header-container {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .header-logo {
      width: 70px;
      height: 70px;
      margin-right: 15px;
      object-fit: contain;
    }

    .header-text {
      text-align: left;
      line-height: 1.4;
    }

    .header-text .title-label {
      font-size: 16px;
      font-weight: bold;
    }

    .header-text .school-name {
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .header-text .school-address {
      font-size: 14px;
    }

    @media print {
      body {
        background: none;
      }

      .legal-page {
        margin: 0;
        box-shadow: none;
      }
    }
  </style>
</head>
<body>


  <div class="legal-page">

    <!-- First DTR Section -->
    <div class="dtr-section">
      <div style="text-align: center; ">
        <img src="logo.jpg" alt="School Logo" style="width: 60px; height: 60px; object-fit: contain; display: block; margin: 0 auto;">
        <div style="text-align: center; line-height: 1.4; margin-top: 4px;">
          <div style="font-size: 14px; font-family: 'Old English Text MT', cursive; position: relative; top: -8px;">Republic of the Philippines</div>
          <div style="font-size: 20px; font-family: 'Old English Text MT', cursive; position: relative; top: -14px;">Department of Education</div>
          <div style="font-size: 16px; position: relative; top: -18px;">Region XII</div>
          <div style="font-size: 14px; position: relative; top: -20px;">DIVISION OF SULTAN KUDARAT</div>
        </div>
      </div>

      <div style="line-height: 1.4; margin-top: 5px;">
        <div style="font-size: 14px">Civil Service Form No. 48</div>         
      </div>
      <div style="text-align: center; line-height: 1.4; margin-top: 5px;"> 
        <div>DAILY TIME RECORD</div>           
      </div>

<div style="text-align: center; margin-top: 10px;">
  <div style="font-size: 14px;">
    <?php echo $rowselect['name']; ?>
  </div>
  <div style="border-bottom: 1px solid #000; width: 300px; margin: 2px auto;"></div>
  <div style="font-size: 12px;">(Name)</div>
</div>


<!-- DTR Info Section -->
<div style="font-size: 10px;margin-top: 4px;">
  <div style="margin-bottom: 6px;">
    <strong>School/District:</strong> Bannawag Elementary School / South President Quirino
  </div>

  <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
    <div><strong>Employee No.:</strong> ____________________</div>
    <div><strong>Div./Stn. Code:</strong> 018-014</div>
  </div>

  <div style="margin-bottom: 6px;">
    <strong>For the month of:</strong> ____________________________
  </div>

  <div style="margin-bottom: 6px;">
    <strong>Official Hours (Regular):</strong> 07:00 AM – 12:00 PM
  </div>

  <div style="margin-bottom: 6px;">
    <strong>For Arrival & Departure (Days):</strong> 01:00 PM – 04:00 PM
  </div>

  <div style="margin-bottom: 6px;">
    <strong>Saturdays:</strong> (as required)
  </div>
</div>
      <table>
        <thead>
          <tr>
            <th rowspan="2">Day</th>
            <th colspan="2">A.M.</th>
            <th colspan="2">P.M.</th>
            <th rowspan="2">Day</th>
            <th rowspan="2">U/T HM</th>
          </tr>
          <tr>
            <td width="15%">Arrival</td>
            <td width="15%">Dept</td>
            <td width="15%">Arrival</td>
            <td width="15%">Dept</td>
          </tr>
        </thead>
        <tbody>
<?php
$month = 6; // June
$year = 2025;

for ($day = 1; $day <= 31; $day++) {
    if (!checkdate($month, $day, $year)) continue;

    $date = "$year-$month-$day";
    $dayName = date('D', strtotime($date)); // Outputs: Sun, Mon, etc.

    $am_in = $structured_logs[$day]['am_in'] ?? '';
    $am_out = $structured_logs[$day]['am_out'] ?? '';
    $pm_in = $structured_logs[$day]['pm_in'] ?? '';
    $pm_out = $structured_logs[$day]['pm_out'] ?? '';

    echo "<tr>
            <td>$day</td>
            <td>$am_in</td>
            <td>$am_out</td>
            <td>$pm_in</td>
            <td>$pm_out</td>
            <td>$dayName</td> <!-- This is the day name -->
            <td></td>
          </tr>";
}
?>



        </tbody>
      </table>
    </div>

    <!-- Second DTR Section -->
    <div class="dtr-section">
      <div class="header-container">
        <img src="logo.jpg" alt="School Logo" class="header-logo">
        <div class="header-text">
          <div class="school-address">Republic of the Philippines</div>
          <div class="school-name">Bannawag Elementary School</div>
          <div class="school-address">Bannawag, President Quirino, S.K.</div>
        </div>
      </div>
      <hr>

      <div class="title-label">Daily Time Record</div>
      <div class="info-line">Name: _______________________________</div>
      <div class="info-line">Designation: ____________________________</div>
      <div class="info-line">Month of ____________ , Year ______</div>

      <table>
        <thead>
          <tr>
            <th rowspan="2">Day</th>
            <th colspan="2">AM</th>
            <th colspan="2">PM</th>
            <th rowspan="2">Remarks</th>
          </tr>
          <tr>
            <th width="15%">IN</th>
            <th width="15%">OUT</th>
            <th width="15%">IN</th>
            <th width="15%">OUT</th>
          </tr>
        </thead>
        <tbody>
          <?php
            for ($day = 1; $day <= 31; $day++) {
              echo "<tr>
                      <td>$day</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>";
            }
          ?>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
