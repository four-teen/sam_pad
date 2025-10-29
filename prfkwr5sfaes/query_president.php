<?php
include '../db.php';
session_start();

if (isset($_POST['load_pending_actions'])) {

  $sql = "SELECT d.id, d.file_code, d.particular, o.office_name, d.date_received, d.status
          FROM tbl_documents_registry d
          LEFT JOIN tbl_office_heads o ON o.office_id = d.origin_office
          WHERE d.received_by = 'President' AND d.status='Received'
          ORDER BY d.date_received DESC";
  
  $result = mysqli_query($conn, $sql);
  $count = 0;

  if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $count++;
      echo "
        <tr>
          <td>{$count}</td>
          <td>{$row['file_code']}</td>
          <td>{$row['particular']}</td>
          <td>{$row['office_name']}</td>
          <td>{$row['date_received']}</td>
          <td><span class='badge bg-warning text-dark'>{$row['status']}</span></td>
        </tr>
      ";
    }
  } else {
    echo "<tr><td colspan='6' class='text-center text-muted'>No pending actions found.</td></tr>";
  }
}
?>
