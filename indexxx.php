<?php
$filename = 'InOutData.csv';

if (!file_exists($filename)) {
    die("File not found: $filename");
}

echo "<style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
      </style>";

echo "<table>";

$row = 0;

if (($handle = fopen($filename, 'r')) !== false) {
    while (($data = fgetcsv($handle)) !== false) {
        if ($row === 0) {
            // Header row
            echo "<thead><tr>";
            foreach ($data as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr></thead><tbody>";
        } else {
            echo "<tr>";
            foreach ($data as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        $row++;
    }
    echo "</tbody></table>";
    fclose($handle);
} else {
    echo "Unable to open file.";
}
?>
