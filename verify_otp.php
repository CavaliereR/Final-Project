<?php
session_start();
include("Database.php");

if (!isset($_SESSION['temp_email'])) {
    header("Location: Register.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);
    
    if ($entered_otp == $_SESSION['verification_code']) {
 
        $email = $_SESSION['temp_email'];
        mysqli_query($conn, "UPDATE users SET is_verified = 1 WHERE email='$email'");
        
      
        unset($_SESSION['temp_email'], $_SESSION['temp_fullname'], $_SESSION['verification_code']);
        
        header("Location: index.php?verified=1");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="card" style="max-width: 400px; margin: 50px auto;">
            <div class="card-body text-center">
                <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                <h4>Verify Your Email</h4>
                <p>We've sent a verification code to:</p>
                <p><strong><?php echo htmlspecialchars($_SESSION['temp_email']); ?></strong></p>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="text" name="otp" placeholder="Enter 6-digit OTP" 
                           class="form-control mb-2 text-center" maxlength="6" required>
                    <button type="submit" class="btn btn-primary w-100">Verify Account</button>
                </form>
                
                <div class="mt-3">
                    <a href="Register.php" class="text-decoration-none">Go Back</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>