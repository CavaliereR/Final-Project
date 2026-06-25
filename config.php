<?php
define('DB_HOST', 'sql200.infinityfree.com');
define('DB_USER', 'if0_42264300');
define('DB_PASS', 'Sedilla@2026');  
define('DB_NAME', 'if0_42264300_onlinequizdb');

function getConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}
?>