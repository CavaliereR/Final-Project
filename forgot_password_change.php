<?php
session_start();
require_once 'Database.php';  // Use Database.php

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header('Location: forgot_password.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    if (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $email = $_SESSION['reset_email'];
        $role = $_SESSION['reset_role'];
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email' AND role = '$role'";
        
        if (mysqli_query($conn, $query)) {
            $message = 'Password updated successfully!';
            
            $_SESSION['userID'] = $_SESSION['reset_userID'];
            $_SESSION['role'] = $role;
            $_SESSION['fullname'] = $_SESSION['reset_fullname'];
            
            unset($_SESSION['reset_email'], $_SESSION['reset_role'], $_SESSION['reset_userID']);
            unset($_SESSION['reset_fullname'], $_SESSION['reset_password']);
            unset($_SESSION['otp_verified'], $_SESSION['otp'], $_SESSION['otp_sent'], $_SESSION['otp_time']);
            
            if ($role == "Teacher") {
                echo '<meta http-equiv="refresh" content="3;url=TeacherDashboard.php">';
            } else {
                echo '<meta http-equiv="refresh" content="3;url=StudentDashboard.php">';
            }
        } else {
            $error = 'Failed to update password. Please try again.';
        }
    }
}
// Rest of HTML...
?>