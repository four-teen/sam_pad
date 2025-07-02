<?php 
ob_start();
include 'db.php';




//get month settings
$get_month = "SELECT * FROM `tblsettings` LIMIT 1";
$runget_month = mysqli_query($conn, $get_month);
$rowget_month = mysqli_fetch_assoc($runget_month);

$mnt = $rowget_month['set_month'];
$yr = $rowget_month['set_year'];

$emp_no = $_GET['emp_no'];
$select = "SELECT * FROM `tbl_biometric_logs` 
           WHERE emp_no='$emp_no' 
           AND YEAR(datetime) = '$yr' 
           AND MONTH(datetime) = '$mnt' 
           ORDER BY datetime ASC";
$runselect = mysqli_query($conn, $select);
// $roget_emp_num = mysqli_fetch_assoc($runselect);


//CHECK EMPLOYEE CLASS
$get_class = "SELECT * FROM `tbl_set_class` WHERE fac_id='$emp_no'";
$runget_class = mysqli_query($conn, $get_class);
$rowget_class = mysqli_fetch_assoc($runget_class);

$get_emp_class = $rowget_class['fac_class']; // 0 for 7 to 4; 1 for 8 to 5


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
    $day = (int)$datetime->format('j');
    $time = $datetime->format('H:i');
    $hour = (int)$datetime->format('H');
    $status = strtolower($log['status']);

    if (!isset($structured_logs[$day])) {
        $structured_logs[$day] = [
            'am_in' => null,
            'am_out' => null,
            'pm_in' => null,
            'pm_out' => null,
        ];
    }

// C/In logic
if ($status === 'c/in') {
    if ($hour < 12) {
        if (!$structured_logs[$day]['am_in'] || strtotime($time) < strtotime($structured_logs[$day]['am_in'])) {
            $structured_logs[$day]['am_in'] = $time;
        }
    } else {
        if (!$structured_logs[$day]['pm_in'] || strtotime($time) < strtotime($structured_logs[$day]['pm_in'])) {
            $structured_logs[$day]['pm_in'] = $time;
        }
    }
}

// C/Out logic
elseif ($status === 'c/out') {
    if ($hour < 12 || ($hour == 12 && !$structured_logs[$day]['pm_in'])) {
        if (!$structured_logs[$day]['am_out'] || strtotime($time) > strtotime($structured_logs[$day]['am_out'])) {
            $structured_logs[$day]['am_out'] = $time;
        }
    } else {
        if (!$structured_logs[$day]['pm_out'] || strtotime($time) > strtotime($structured_logs[$day]['pm_out'])) {
            $structured_logs[$day]['pm_out'] = $time;
        }
    }
}

}

// 2025-06-02 11:14:06

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
<div style="font-size: 10px; margin-top: 4px;">
  <div style="margin-bottom: 3px;">
    <strong>School/District:</strong> Bannawag Elementary School / South President Quirino
  </div>

  <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
    <div><strong>Employee No.:</strong> <u><?php echo $_GET['id_number'] ?></u></div>
    <div><strong>Div./Stn. Code:</strong> 018-014</div>
  </div>

  <div style="margin-bottom: 3px;">
    <strong>For the month of:</strong> <u><?php echo date('F Y', strtotime("$yr-$mnt-01")); ?></u>
  </div>

  <div style="margin-bottom: 3px;">
    <strong>Official Hours (Regular):</strong> 07:00 AM – 12:00 PM
  </div>

  <div style="margin-bottom: 3px;">
    <strong>For Arrival & Departure (Days):</strong> 01:00 PM – 04:00 PM
  </div>

  <div style="margin-bottom: 3px;">
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
                  $month = $mnt; // June
                  $year = $yr;

                  $total_ut_minutes = 0;

                  $lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 30 or 31

                  // SET shift range based on class
                  if ($get_emp_class == 0) {
                      $am_shift_start = '07:00';
                      $am_shift_end = '12:00';
                      $pm_shift_start = '13:00';
                      $pm_shift_end = '16:00';
                  } else {
                      $am_shift_start = '08:00';
                      $am_shift_end = '12:00';
                      $pm_shift_start = '13:00';
                      $pm_shift_end = '17:00';
                  }

for ($day = 1; $day <= $lastDay; $day++) {
    if (!checkdate($month, $day, $year)) continue;

    $date = "$year-$month-$day";
    $dayName = date('D', strtotime($date));

    $am_in = $structured_logs[$day]['am_in'] ?? '';
    $am_out = $structured_logs[$day]['am_out'] ?? '';
    $pm_in = $structured_logs[$day]['pm_in'] ?? '';
    $pm_out = $structured_logs[$day]['pm_out'] ?? '';

    $am_ut = 0;
    $pm_ut = 0;

    $all_empty = empty($am_in) && empty($am_out) && empty($pm_in) && empty($pm_out);

    if (!$all_empty) {
        $am_start = new DateTime("$date $am_shift_start");
        $am_end = new DateTime("$date $am_shift_end");

        if ($am_in) {
            $actual_am_in = new DateTime("$date $am_in");
            if ($actual_am_in > $am_start) {
                $am_ut += ($actual_am_in->getTimestamp() - $am_start->getTimestamp()) / 60;
            }
        } else {
            $am_ut += ($am_end->getTimestamp() - $am_start->getTimestamp()) / 60;
        }

        if ($am_out) {
            $actual_am_out = new DateTime("$date $am_out");
            if ($actual_am_out < $am_end) {
                $am_ut += ($am_end->getTimestamp() - $actual_am_out->getTimestamp()) / 60;
            }
        } else {
            $am_ut += ($am_end->getTimestamp() - $am_start->getTimestamp()) / 60;
        }

        $pm_start = new DateTime("$date $pm_shift_start");
        $pm_end = new DateTime("$date $pm_shift_end");

        if (empty($pm_in) && empty($pm_out)) {
            $pm_ut += ($pm_end->getTimestamp() - $pm_start->getTimestamp()) / 60;
        } else {
            if ($pm_in) {
                $actual_pm_in = new DateTime("$date $pm_in");
                if ($actual_pm_in > $pm_start) {
                    $pm_ut += ($actual_pm_in->getTimestamp() - $pm_start->getTimestamp()) / 60;
                }
            } else {
                $pm_ut += ($pm_end->getTimestamp() - $pm_start->getTimestamp()) / 60;
            }

            if ($pm_out) {
                $actual_pm_out = new DateTime("$date $pm_out");
                if ($actual_pm_out < $pm_end) {
                    $pm_ut += ($pm_end->getTimestamp() - $actual_pm_out->getTimestamp()) / 60;
                }
            } else {
                $pm_ut += ($pm_end->getTimestamp() - $pm_start->getTimestamp()) / 60;
            }
        }

        $ut_minutes = round($am_ut + $pm_ut);
        $total_ut_minutes += $ut_minutes;

        $ut_display = ($ut_minutes > 0) ? sprintf("%d:%02d", floor($ut_minutes / 60), $ut_minutes % 60) : "";
    } else {
        $ut_display = "";
    }

    echo '<tr>
            <td>'.$day.'</td>
            <td>'.$am_in.'</td>
            <td>'.$am_out.'</td>
            <td>'.($pm_in ? date("H:i", strtotime($pm_in)) : '').'</td>
            <td>'.($pm_out ? date("H:i", strtotime($pm_out)) : '').'</td>
            <td>'.$dayName.'</td>
            <td>'.$pm_ut.'</td>
          </tr>';
}
// Final total undertime row
$total_ut_display = ($total_ut_minutes > 0)
    ? sprintf("%d:%02d", floor($total_ut_minutes / 60), $total_ut_minutes % 60)
    : "";

                  echo "<tr style='font-weight: bold; background-color: #e0e0e0'>
                          <td colspan='6' class='text-right'>Total Undertime:</td>
                          <td>$total_ut_display</td>
                        </tr>";

          ?>



        </tbody>
      </table>
<div style="text-align: center; margin-top: 10px;">
  <span style="font-size: 11px;">
    I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
  </span>
</div>
<div style="text-align: center; margin-top: 10px;">
  <div style="border-bottom: 1px solid #000; width: 300px; margin: 0 auto;">
    <?php echo $rowselect['name']; ?>
  </div>
  <div style="font-size: 12px;">Employee</div>
</div>
<br>
<div style="text-align: center; margin-top: 10px;">
  <div style="border-bottom: 1px solid #000; width: 300px; margin: 0 auto;">
    <b>DEXTER V. ALBERTO, MAED</b>
  </div>
  <div style="font-size: 12px;">School Principal I</div>
</div>
    </div>

    <!-- Second DTR Section -->
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
<div style="font-size: 10px; margin-top: 4px;">
  <div style="margin-bottom: 3px;">
    <strong>School/District:</strong> Bannawag Elementary School / South President Quirino
  </div>

  <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
    <div><strong>Employee No.:</strong> <u><?php echo $_GET['id_number'] ?></u></div>
    <div><strong>Div./Stn. Code:</strong> 018-014</div>
  </div>

  <div style="margin-bottom: 3px;">
    <strong>For the month of:</strong> <u><?php echo date('F Y', strtotime("$yr-$mnt-01")); ?></u>
  </div>

  <div style="margin-bottom: 3px;">
    <strong>Official Hours (Regular):</strong> 07:00 AM – 12:00 PM
  </div>

  <div style="margin-bottom: 3px;">
    <strong>For Arrival & Departure (Days):</strong> 01:00 PM – 04:00 PM
  </div>

  <div style="margin-bottom: 3px;">
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

                  $total_ut_minutes = 0;

                  $lastDay = cal_days_in_month(CAL_GREGORIAN, $month, $year); // 30 or 31

                  for ($day = 1; $day <= $lastDay; $day++) {
                      if (!checkdate($month, $day, $year)) continue;

                      $date = "$year-$month-$day";
                      $dayName = date('D', strtotime($date));

                      $am_in = $structured_logs[$day]['am_in'] ?? '';
                      $am_out = $structured_logs[$day]['am_out'] ?? '';
                      $pm_in = $structured_logs[$day]['pm_in'] ?? '';
                      $pm_out = $structured_logs[$day]['pm_out'] ?? '';

                      $ut_minutes = 0;

                      $all_empty = empty($am_in) && empty($am_out) && empty($pm_in) && empty($pm_out);

                      if (!$all_empty) {
                          // AM Range
                          $am_start = new DateTime("$date 07:00");
                          $am_end = new DateTime("$date 12:00");

                          if ($am_in) {
                              $actual_am_in = new DateTime("$date $am_in");
                              if ($actual_am_in > $am_start) {
                                  $ut_minutes += ($actual_am_in->getTimestamp() - $am_start->getTimestamp()) / 60;
                              }
                          } else {
                              $ut_minutes += 300;
                          }

                          if ($am_out) {
                              $actual_am_out = new DateTime("$date $am_out");
                              if ($actual_am_out < $am_end) {
                                  $ut_minutes += ($am_end->getTimestamp() - $actual_am_out->getTimestamp()) / 60;
                              }
                          } else {
                              $ut_minutes += 300;
                          }

                          // PM Range
                          $pm_start = new DateTime("$date 13:00");
                          $pm_end = new DateTime("$date 16:00");

                          if ($pm_in) {
                              $actual_pm_in = new DateTime("$date $pm_in");
                              if ($actual_pm_in > $pm_start) {
                                  $ut_minutes += ($actual_pm_in->getTimestamp() - $pm_start->getTimestamp()) / 60;
                              }
                          } else {
                              $ut_minutes += 180;
                          }

                          if ($pm_out) {
                              $actual_pm_out = new DateTime("$date $pm_out");
                              if ($actual_pm_out < $pm_end) {
                                  $ut_minutes += ($pm_end->getTimestamp() - $actual_pm_out->getTimestamp()) / 60;
                              }
                          } else {
                              $ut_minutes += 180;
                          }

                          $ut_minutes = round($ut_minutes);
                          $total_ut_minutes += $ut_minutes;

                          $ut_display = ($ut_minutes > 0) ? sprintf("%d:%02d", floor($ut_minutes / 60), $ut_minutes % 60) : "";
                      } else {
                          $ut_display = "";
                      }

                      echo '<tr>
                              <td>'.$day.'</td>
                              <td>'.$am_in.'</td>
                              <td>'.$am_out.'</td>
                              <td>'.($pm_in ? date("h:i", strtotime($pm_in)) : '').'</td>
                              <td>'.($pm_out ? date("h:i", strtotime($pm_out)) : '').'</td>
                              <td>'.$dayName.'</td>
                              <td>'.$ut_display.'</td>
                            </tr>';
                  }

                  // FINAL TOTAL ROW
                  $total_ut_display = ($total_ut_minutes > 0) ? sprintf("%d:%02d", floor($total_ut_minutes / 60), $total_ut_minutes % 60) : "";

                  echo "<tr style='font-weight: bold; background-color: #e0e0e0'>
                          <td colspan='6' class='text-right'>Total Undertime:</td>
                          <td>$total_ut_display</td>
                        </tr>";

          ?>
        </tbody>
      </table>
<div style="text-align: center; margin-top: 10px;">
  <span style="font-size: 11px;">
    I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.
  </span>
</div>
<div style="text-align: center; margin-top: 10px;">
  <div style="border-bottom: 1px solid #000; width: 300px; margin: 0 auto;">
    <?php echo $rowselect['name']; ?>
  </div>
  <div style="font-size: 12px;">Employee</div>
</div>
<br>
<div style="text-align: center; margin-top: 10px;">
  <div style="border-bottom: 1px solid #000; width: 300px; margin: 0 auto;">
    <b>DEXTER V. ALBERTO, MAED</b>
  </div>
  <div style="font-size: 12px;">School Principal I</div>
</div>
     
    </div>

  </div>
</body>
</html>
