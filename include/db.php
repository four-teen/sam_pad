<?php

$servername = "localhost";
$username = "root";
$password = "vertrigo";
$dbase = "payroll_db";

$conn = mysqli_connect($servername, $username, $password, $dbase);

// Check connection
if (!$conn) {
    die("Connection failed: ask me " . mysqli_connect_error());
}

// ✅ Set character set to utf8mb4 to support special characters like Ñ
mysqli_set_charset($conn, "utf8mb4");

?>
