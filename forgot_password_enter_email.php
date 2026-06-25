<?php
session_start();

if (!isset($_SESSION['reset_fullname'])) {
    header('Location: forgot_password.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $_SESSION['reset_email'] = $email;
        unset($_SESSION['otp_sent']);
        header('Location: forgot_password_enter_otp.php');
        exit;
    }
}

$fullname = $_SESSION['reset_fullname'];
$currentEmail = $_SESSION['reset_email'] ?? '';
?>

<html>
<head>
    <title>Enter Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .email-container {
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
        }
        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.15);
        }
        .alert {
            border-radius: 10px;
        }
        .back-link {
            color: #8B0000;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .btn-send {
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
        .btn-send:hover {
            background: #6d0000 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-container">
            <div class="card">
                <div class="card-header">
                    <h4>🏫 Email Verification</h4>
                </div>
                <div class="card-body">
                    <h5>Enter Your Email</h5>
                    <p class="text-muted">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="forgot_password_enter_email.php">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control" name="email" 
                                value="<?php echo htmlspecialchars($currentEmail); ?>" required>
                        </div>
                        <button type="submit" class="btn-send">
                            <i class="fas fa-paper-plane me-2"></i> Send OTP
                        </button>
                    </form>
                    
                    <div class="mt-3">
                        <a href="forgot_password.php" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>