<?php
session_start();


$connection = mysqli_connect(
    "localhost","root","","onlinequizdb");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

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
        
        if (mysqli_query($connection, $query)) {
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

mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
body{
    background:#f8f9fa;
}

.card{
    border:none;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.card-header{
    background:#dc3545 !important;
    color:white !important;
}

.btn-primary,
.btn-success,
.bg-primary{
    background:#dc3545 !important;
    border-color:#dc3545 !important;
}

.btn-primary:hover,
.btn-success:hover{
    background:#bb2d3b !important;
    border-color:#bb2d3b !important;
}

.form-control:focus{
    border-color:#dc3545;
    box-shadow:0 0 0 .25rem rgba(220,53,69,.15);
}

.table thead th{
    background:#dc3545 !important;
    color:white !important;
}

.table tbody tr:hover{
    background:#fff5f5;
}

.alert{
    border-radius:12px;
}

.btn{
    border-radius:10px;
}

.badge.bg-primary{
    background:#dc3545 !important;
}
</style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f0f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .school-header {
            background-color: #8B0000;
            color: white;
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .reset-container {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-top: 80px;
            max-width: 450px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-update {
            background-color: #28a745;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            background-color: #218838;
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="school-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>
                </a>
                <h4 class="flex-grow-1 text-center">🏫 Reset Password</h4>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="reset-container">
            <h3 class="text-center">Set New Password</h3>
            <p class="text-center text-muted">User: <?php echo htmlspecialchars($_SESSION['reset_fullname'] ?? ''); ?></p>
            
            <?php if($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $message; ?>
                </div>
                <p class="text-center">Redirecting to dashboard...</p>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!$message): ?>
                <form method="post" action="forgot_password_change.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                        <small class="text-muted">Must be at least 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn-update">
                        <i class="fas fa-save"></i> Update Password
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-decoration-none">
                    <i class="fas fa-home"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

</body>
</html>