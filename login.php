<?php
session_start();

// Redirect if already logged in as student
if(isset($_SESSION['name']) && isset($_SESSION['role']) && $_SESSION['role'] == 'student') {
    header("Location: quiz.php");
    exit();
}

// Database connection
$connection = mysqli_connect(
    "localhost",
    "root",
    "",
    "qez"
);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

if(isset($_POST['name']) && isset($_POST['password']))
{
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $password = $_POST['password'];
    $role = 'student';

    $query = "SELECT * FROM student WHERE name = '$name'";
    $result = mysqli_query($connection, $query);

    if($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        if($password == $row['password']) {
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
            $_SESSION['user_id'] = $row['ID'];
            
            header("Location: quiz.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Student account not found.";
    }

    mysqli_close($connection);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Quiz System</title>
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
        
        .school-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .login-container {
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
        
        .login-container .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .login-container .icon-circle i {
            font-size: 40px;
            color: #8B0000;
        }
        
        .login-container h3 {
            text-align: center;
            color: #333;
            font-weight: 600;
        }
        
        .login-container .sub-text {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
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
        
        .btn-login {
            background-color: #8B0000;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #6d0000;
        }
        
        .btn-login i {
            margin-right: 8px;
        }
        
        .back-link {
            color: #8B0000;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .forgot-password {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-password:hover {
            color: #8B0000;
        }
        
        .alert-danger {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- School Header -->
    <div class="school-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>
                </a>
                <h4 class="flex-grow-1 text-center">University of the East</h4>
            </div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="container">
        <div class="login-container">
            <div class="icon-circle">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3>Student Login</h3>
            <p class="sub-text">Sign in to access your quizzes</p>
            
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="login.php">
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Enter your name" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="forgot_password.php?role=student" class="forgot-password">
                        <i class="fas fa-key"></i> Forgot Password?
                    </a>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login as Student
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Role Selection
                </a>
            </div>
        </div>
    </div>

</body>
</html>