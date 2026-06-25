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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .otp-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            background: #8B0000 !important;
            color: white !important;
            text-align: center;
            padding: 20px;
            border: none;
        }
        .card-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
            text-align: center;
        }
        .btn-primary {
            background: #8B0000 !important;
            border-color: #8B0000 !important;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #6d0000 !important;
            border-color: #6d0000 !important;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
        }
        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.15);
        }
        .alert {
            border-radius: 10px;
        }
        .icon-circle {
            width: 70px;
            height: 70px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        .icon-circle i {
            font-size: 35px;
            color: #8B0000;
        }
        .back-link {
            color: #8B0000;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="otp-container">
            <div class="card">
                <div class="card-header">
                    <h4>📧 Verify Your Email</h4>
                </div>
                <div class="card-body">
                    <div class="icon-circle">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <p>We've sent a verification code to:</p>
                    <p><strong><?php echo htmlspecialchars($_SESSION['temp_email - verify_otp.php:132']); ?></strong></p>
                    
                    <?php if($error): ?>
                        <div class="alert alertdanger - verify_otp.php:135"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enter 6-digit OTP</label>
                            <input type="text" name="otp" class="form-control" placeholder="000000" maxlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle me-2"></i> Verify Account
                        </button>
                    </form>
                    
                    <div class="mt-3">
                        <a href="Register.php" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>