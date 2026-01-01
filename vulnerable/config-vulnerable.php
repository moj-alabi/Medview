<?php
// VULNERABLE DATABASE CONFIG - FOR TRAINING ONLY
// WARNING: This configuration exposes database credentials and has no security measures

define('DB_SERVER','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME', 'medview_vulnerable');

// Vulnerable connection - displays detailed errors
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Bad practice: Exposing detailed connection errors
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error() . "<br>" .
        "Server: " . DB_SERVER . "<br>" .
        "Database: " . DB_NAME . "<br>" .
        "User: " . DB_USER);
}

// Vulnerable: No error suppression, detailed errors exposed
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
