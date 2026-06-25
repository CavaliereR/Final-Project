<?php
session_start();
include("Database.php");

// Check if user is already logged in
if (isset($_SESSION['userID'])) {
    if ($_SESSION['role'] == "Teacher") {
        header("Location: TeacherDashboard.php");
    } else {
        header("Location: StudentDashboard.php");
    }
    exit();
}

// Check for remember me cookies first (auto-login)
if (isset($_COOKIE['user_email']) && isset($_COOKIE['user_token'])) {
    $email = mysqli_real_escape_string($conn, $_COOKIE['user_email']);
    $token = $_COOKIE['user_token'];
    
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Verify the token
        if ($token == md5($row['password'] . $row['email'])) {
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['email'] = $row['email'];
            
            if ($row['role'] == "Teacher") {
                header("Location: TeacherDashboard.php");
            } else {
                header("Location: StudentDashboard.php");
            }
            exit();
        } else {
            // Invalid token, clear cookies
            setcookie('user_email', '', time() - 3600, "/");
            setcookie('user_token', '', time() - 3600, "/");
        }
    } else {
        // User doesn't exist, clear cookies
        setcookie('user_email', '', time() - 3600, "/");
        setcookie('user_token', '', time() - 3600, "/");
    }
}

$error = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

    // If Remember Me is unchecked, clear any existing cookies
    if (!$remember) {
        setcookie('user_email', '', time() - 3600, "/");
        setcookie('user_token', '', time() - 3600, "/");
    }

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['email'] = $row['email'];

            // Set cookies if Remember Me is checked
            if ($remember) {
                // Create a secure token based on user data
                $token = md5($row['password'] . $row['email']);
                setcookie('user_email', $email, time() + (86400 * 30), "/");
                setcookie('user_token', $token, time() + (86400 * 30), "/");
            }

            if ($row['role'] == "Teacher") {
                header("Location: TeacherDashboard.php");
            } else {
                header("Location: StudentDashboard.php");
            }
            exit();
        } else {
            $error = "Invalid Password";
        }
    } else {
        $error = "Email not found";
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
        .login-container {
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
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.15);
        }
        .form-check-input:checked {
            background-color: #8B0000;
            border-color: #8B0000;
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
        .forgot-link {
            color: #8B0000;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <h4>🔐 Quiz System Login</h4>
                </div>
                <div class="card-body">
                    <!-- FIXED: Only show error if it's not empty -->
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" 
                                value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''; ?>"
                                placeholder="Enter your email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Enter your password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input"
                                <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
                            <label for="remember" class="form-check-label">Remember Me</label>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </form>
                    
                    <div class="mt-3">
                        <a href="Register.php" class="btn btn-success w-100">
                            <i class="fas fa-user-plus me-2"></i> Register New Account
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="forgot_password.php" class="forgot-link">
                            <i class="fas fa-key me-1"></i> Forgot Password?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>