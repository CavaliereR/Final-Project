<?php
session_start();

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

$message = '';
$error = '';
$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $role = mysqli_real_escape_string($connection, $_POST['role']);
    
    // Determine which table to query
    $table = ($role == 'student') ? 'student' : 'teacher';
    
    // Check if user exists in the appropriate table using 'name' column
    $query = "SELECT * FROM $table WHERE name = '$name'";
    $result = mysqli_query($connection, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Store user info in session for the reset process
        $_SESSION['reset_name'] = $name;
        $_SESSION['reset_role'] = $role;
        $_SESSION['reset_table'] = $table;
        
        // Redirect to enter email page
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
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            background-color: #6d0000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="school-header">
        <div class="container">
            <h4 class="text-center">🏫 Password Recovery</h4>
        </div>
    </div>

    <div class="container">
        <div class="forgot-container">
            <div class="icon-circle">
                <i class="fas fa-key"></i>
            </div>
            <h3 class="text-center">Forgot Password</h3>
            <p class="text-center text-muted">Enter your name to reset your password</p>
            
            <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" action="forgot_password.php">
                <div class="mb-3">
                    <label class="form-label fw-bold">Name</label>
                    <input type="text" class="form-control" name="name" required 
                           placeholder="Enter your name">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Role</label>
                    <select class="form-select" name="role" required>
                        <option value="">Select your role</option>
                        <option value="student" <?php echo ($role == 'student') ? 'selected' : ''; ?>>Student</option>
                        <option value="teacher" <?php echo ($role == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-reset">
                    <i class="fas fa-paper-plane"></i> Continue
                </button>
            </form>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>