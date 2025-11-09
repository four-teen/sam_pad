<?php
ob_start();
session_start();
include 'db.php';

// 🧠 Define log file path
$logFile = __DIR__ . '/login_log.txt';
if (!file_exists($logFile)) {
    file_put_contents($logFile, "=== Login Log File Created at " . date('Y-m-d H:i:s') . " ===\n");
}

// 🪵 Write to log (compact style)
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// --- START ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim(mysqli_real_escape_string($conn, $_POST['password']));

    $sql = "SELECT * FROM tbl_accounts WHERE acc_username='$username' AND acc_status='Active' LIMIT 1";
    $query = mysqli_query($conn, $sql);

    if ($query && mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);

        if (password_verify($password, $row['acc_password'])) {

            // 🧩 Set sessions
            $_SESSION['acc_id']     = $row['acc_id'];
            $_SESSION['username']   = $row['acc_username'];
            $_SESSION['fullname']   = $row['acc_fullname'];
            $_SESSION['role']       = $row['acc_role'];
            $_SESSION['officeid']   = $row['acc_role'] ?? null;

            // 🕒 Update last login
            mysqli_query($conn, "UPDATE tbl_accounts SET last_login_at = NOW() WHERE acc_id = '{$row['acc_id']}'");

            // 🌐 Device fingerprint
            $ip         = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';
            $agent      = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Agent';
            $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Unknown';
            $deviceType = (preg_match('/mobile/i', $agent)) ? 'Mobile' : ((preg_match('/tablet/i', $agent)) ? 'Tablet' : 'Desktop');
            $fingerprintHash = hash('sha256', $ip . '|' . $agent . '|' . $acceptLang);

            // 🧾 Write summarized log
            writeLog("---- Login attempt started | Request method: POST detected | Username received: $username, Account found: ID={$row['acc_id']} Role={$row['acc_role']} ----");
            writeLog("✅ Login successful | IP: $ip | Device: $deviceType | Lang: $acceptLang");
            writeLog("🧩 Browser: $agent");
            writeLog("🔐 Fingerprint Hash: $fingerprintHash");

            // ✅ Redirect logic
            switch ($row['acc_role']) {
                case '66':
                    writeLog("Redirecting to administrator dashboard...");
                    $redirect = "administrator/index.php";
                    break;
                case '67':
                    writeLog("Redirecting to records dashboard...");
                    $redirect = "records/index.php";
                    break;
                case '68':
                    writeLog("Redirecting to PAD dashboard...");
                    $redirect = "pad/index.php";
                    break;
                case '1':
                    writeLog("Redirecting to PAD dashboard...");
                    $redirect = "prfkwr5sfaes/index.php";
                    break; 
                case '9':
                    writeLog("Redirecting to directors..."); //director dashboard
                    $redirect = "dir3851ec51Wtor/index.php";
                    break; 

                default:
                    $_SESSION['status'] = "Unknown role.";
                    $redirect = "index.php";
                    break;
            }

            // 🚀 Redirect safely
            if (!headers_sent()) {
                header("Location: $redirect");
                exit();
            } else {
                echo "<script>window.location.href='$redirect';</script>";
                exit();
            }

        } else {
            $_SESSION['status'] = "Incorrect password.";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "Account not found or inactive.";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

ob_end_flush();
?>
