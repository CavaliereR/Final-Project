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
</head>
<body>
    <div class="container mt-5">
        <div class="card" style="max-width: 400px; margin: 50px auto;">
            <div class="card-body">
                <h4 class="text-center mb-4">📝 Register New Account</h4>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="text" name="fullname" placeholder="Full Name" 
                           class="form-control mb-2" required>
                    
                    <input type="email" name="email" placeholder="Email" 
                           class="form-control mb-2" required>
                    
                    <input type="password" name="password" placeholder="Password (min 6 chars)" 
                           class="form-control mb-2" minlength="6" required>
                    
                    <select name="role" class="form-control mb-2" required>
                        <option value="">Select Role</option>
                        <option value="Student">Student</option>
                        <option value="Teacher">Teacher</option>
                    </select>
                    
                    <input type="submit" name="register" value="Register" 
                           class="btn btn-success w-100">
                    
                    <div class="text-center mt-2">
                        <a href="index.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>