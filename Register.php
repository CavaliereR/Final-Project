<?php
session_start();
include("Database.php");

$message = '';
$error = '';

if(isset($_POST['register']))
{
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }

    else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0) {
            $error = "Email already registered. Please use a different email.";
        } else {
            $password = password_hash($password, PASSWORD_DEFAULT);
            
            $verification_code = (string)random_int(100000, 999999);
            

            $sql = "INSERT INTO users (fullname, email, password, role, verification_code, is_verified) 
                    VALUES ('$fullname', '$email', '$password', '$role', '$verification_code', 0)";
            
            if(mysqli_query($conn, $sql)) {
                $subject = "Verify Your Email - Quiz System";
                $msg = "Hello " . $fullname . ",\n\n";
                $msg .= "Thank you for registering on the Quiz System.\n\n";
                $msg .= "Your verification code is: " . $verification_code . "\n\n";
                $msg .= "Please enter this code to verify your account.";
                $headers = "From: noreply@quizsystem.com\r\n";
                
                if(mail($email, $subject, $msg, $headers)) {
                    $_SESSION['temp_email'] = $email;
                    $_SESSION['temp_fullname'] = $fullname;
                    $_SESSION['verification_code'] = $verification_code;
                    
                    header("Location: verify_otp.php");
                    exit();
                } else {
                    $error = "Registration successful but failed to send verification email. Please contact admin.";
                }
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
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
        .register-container {
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
        }
        .btn-primary, .btn-success {
            background: #8B0000 !important;
            border-color: #8B0000 !important;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover, .btn-success:hover {
            background: #6d0000 !important;
            border-color: #6d0000 !important;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus, .form-select:focus {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="card">
                <div class="card-header">
                    <h4>📝 Register New Account</h4>
                </div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alertdanger - Register.php:139"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if($message): ?>
                        <div class="alert alertsuccess - Register.php:143"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="fullname" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password (min 6 chars)" minlength="6" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Select Role</option>
                                <option value="Student">Student</option>
                                <option value="Teacher">Teacher</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-success w-100">
                            <i class="fas fa-user-plus me-2"></i> Register
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="back-link">
                            <i class="fas fa-arrow-left me-1"></i> Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>