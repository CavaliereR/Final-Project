<?php
session_start();
require_once 'Database.php';  // Use Database.php instead of hardcoded connection

$message = '';
$error = '';
$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $query = "SELECT * FROM users WHERE fullname = '$fullname' AND role = '$role'";
    $result = mysqli_query($conn, $query);
    
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Quiz System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .forgot-container {
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
        .btn-reset {
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
        .btn-reset:hover {
            background: #6d0000 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-container">
            <div class="card">
                <div class="card-header">
                    <h4>🏫 Password Recovery</h4>
                </div>
                <div class="card-body">
                    <div class="icon-circle">
                        <i class="fas fa-key"></i>
                    </div>
                    <h5>Forgot Password</h5>
                    <p class="text-muted">Enter your full name and role to reset your password</p>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="forgot_password.php">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control" name="fullname" required placeholder="Enter your full name">
                        </div>
                        
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="">Select your role</option>
                                <option value="Student" <?php echo ($role == 'Student') ? 'selected' : ''; ?>>Student</option>
                                <option value="Teacher" <?php echo ($role == 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-reset">
                            <i class="fas fa-paper-plane me-2"></i> Continue
                        </button>
                    </form>
                    
                    <div class="mt-3">
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