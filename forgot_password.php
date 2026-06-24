<?php
session_start();


$connection = mysqli_connect(
    "localhost",
    "root",
    "",
    "onlinequizdb" 
);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = '';
$error = '';
$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($connection, $_POST['fullname']);
    $role = mysqli_real_escape_string($connection, $_POST['role']);

    $query = "SELECT * FROM users WHERE fullname = '$fullname' AND role = '$role'";
    $result = mysqli_query($connection, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        $_SESSION['reset_fullname'] = $fullname;
        $_SESSION['reset_role'] = $role;
        $_SESSION['reset_userID'] = $user['userID'];
        $_SESSION['reset_password'] = $user['password'];
        $_SESSION['reset_email'] = $user['email']; 
        
        header('Location: forgot_password_enter_email.php');
        exit;
    } else {
        $error = ucfirst($role) . ' account not found.';
    }
    
    mysqli_close($connection);
}
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
    <title>Forgot Password - Quiz System</title>
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
        
        .forgot-container {
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
        
        .forgot-container .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .forgot-container .icon-circle i {
            font-size: 40px;
            color: #8B0000;
        }
        
        .btn-reset {
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
        
        .btn-reset:hover {
            background-color: #6d0000;
        }
        
        .alert-danger {
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
                <h4 class="flex-grow-1 text-center">🏫 Password Recovery</h4>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="forgot-container">
            <div class="icon-circle">
                <i class="fas fa-key"></i>
            </div>
            <h3 class="text-center">Forgot Password</h3>
            <p class="text-center text-muted">Enter your full name and role to reset your password</p>
            
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="forgot_password.php">
                <div class="mb-3">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" class="form-control" name="fullname" required 
                           placeholder="Enter your full name">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Role</label>
                    <select class="form-select" name="role" required>
                        <option value="">Select your role</option>
                        <option value="Student" <?php echo ($role == 'Student') ? 'selected' : ''; ?>>Student</option>
                        <option value="Teacher" <?php echo ($role == 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-reset">
                    <i class="fas fa-paper-plane"></i> Continue
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

</body>
</html>