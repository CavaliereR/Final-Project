<?php
session_start();

if (!isset($_SESSION['reset_name'])) {
    header('Location: forgot_password.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim($_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $_SESSION['reset_email'] = $email;
        unset($_SESSION['otp_sent']);
        header('Location: forgot_password_enter_otp.php');
        exit;
    }
}

$name = $_SESSION['reset_name'];
$currentEmail = $_SESSION['reset_email'] ?? '';
?>

<html>
<head>
    <title>Enter Email</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .email-container {
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
        .btn-send {
            background-color: #8B0000;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-send:hover {
            background-color: #6d0000;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="school-header">
        <div class="container">
            <h4 class="text-center">🏫 Email Verification</h4>
        </div>
    </div>

    <div class="container">
        <div class="email-container">
            <h3 class="text-center">Enter Your Email</h3>
            <p class="text-center text-muted">Welcome, <?php echo htmlspecialchars($name); ?>!</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="forgot_password_enter_email.php">
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo htmlspecialchars($currentEmail); ?>" required>
                </div>
                <button type="submit" class="btn-send">
                    <i class="fas fa-paper-plane"></i> Send OTP
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>