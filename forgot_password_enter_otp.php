<?php
session_start();

if (!isset($_SESSION['reset_fullname'])) {
    header('Location: forgot_password.php');
    exit;
}

if (!isset($_SESSION['reset_email'])) {
    header('Location: forgot_password_enter_email.php');
    exit;
}

$senderemail = $_SESSION['reset_email'];
$fullname = $_SESSION['reset_fullname'];

$message = '';
$error = '';

if (!isset($_SESSION['otp_sent'])) {
    $_SESSION['otp'] = (string)random_int(100000, 999999);

    $subject = 'Did you ask for a change of password?';
    $msg = "Hello " . $fullname . ",\n\n";
    $msg .= 'Your OTP is: ' . $_SESSION['otp'] . "\n\n";
    $msg .= 'This OTP will expire after 10 minutes.';
    $headers = 'From: noreply@quizsystem.com\r\n' .
               'Reply-To: noreply@quizsystem.com\r\n' .
               'X-Mailer: PHP/' . phpversion();

    if (mail($senderemail, $subject, $msg, $headers)) {
        $message = 'Email Sent';
    } else {
        $message = 'Email sending failed';
    }

    $_SESSION['otp_sent'] = true;
    $_SESSION['otp_time'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enteredotp'])) {
    $enteredOtp = trim($_POST['enteredotp']);
    
    if (isset($_SESSION['otp_time']) && (time() - $_SESSION['otp_time']) > 600) {
        $error = 'OTP has expired. Please request a new one.';
        unset($_SESSION['otp_sent'], $_SESSION['otp'], $_SESSION['otp_time']);
    } elseif ($enteredOtp === $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        header('Location: forgot_password_change.php');
        exit;
    } else {
        $error = 'OTP does not match. Please try again.';
    }
}
?>

<html>
<head>
    <title>Enter your OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .btn-verify {
            background: #8B0000 !important;
            border-color: #8B0000 !important;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-verify:hover {
            background: #6d0000 !important;
        }
        .btn-resend {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            border: none;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-resend:hover {
            background: #5a6268;
            color: white;
        }
        .info-box {
            background: #cce5ff;
            border: 1px solid #b8daff;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            text-align: left;
        }
        .otp-timer {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="otp-container">
            <div class="card">
                <div class="card-header">
                    <h4>🏫 OTP Verification</h4>
                </div>
                <div class="card-body">
                    <div class="icon-circle">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Enter Your OTP</h5>
                    <p class="text-muted">We've sent a verification code to your email</p>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="info-box">
                        <i class="fas fa-envelope me-2"></i>
                        We have sent an email to <strong><?php echo htmlspecialchars($senderemail); ?></strong> with the OTP.
                    </div>
                    
                    <p class="otp-timer">
                        <i class="fas fa-clock me-1"></i> OTP expires in 10 minutes
                    </p>

                    <form method="post" action="forgot_password_enter_otp.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Enter OTP Code</label>
                            <input type="text" class="form-control" name="enteredotp" 
                                placeholder="000000" maxlength="6" required>
                            <small class="text-muted">Enter the 6-digit code sent to your email</small>
                        </div>

                        <button type="submit" class="btn-verify">
                            <i class="fas fa-check-circle me-2"></i> Verify OTP
                        </button>
                    </form>
                    
                    <div class="mt-3">
                        <a href="forgot_password_enter_email.php" class="btn-resend">
                            <i class="fas fa-redo me-1"></i> Resend OTP
                        </a>
                    </div>
                    
                    <div class="mt-3">
                        <a href="forgot_password.php" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i> Start Over
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>