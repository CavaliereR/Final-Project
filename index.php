<?php
session_start();
include("Database.php");


if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;

  
    if (!$remember) {
        setcookie('user_email', '', time() - 3600, "/");
        setcookie('user_password', '', time() - 3600, "/");
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

            if ($remember) {
                setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days
                setcookie('user_password', $password, time() + (86400 * 30), "/");
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


if (!isset($_SESSION['userID']) && isset($_COOKIE['user_email']) && isset($_COOKIE['user_password'])) {
    $email = $_COOKIE['user_email'];
    $password = $_COOKIE['user_password'];
    
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $row['password'])) {
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
            setcookie('user_email', '', time() - 3600, "/");
            setcookie('user_password', '', time() - 3600, "/");
        }
    } else {
        setcookie('user_email', '', time() - 3600, "/");
        setcookie('user_password', '', time() - 3600, "/");
    }
}
?>

<!DOCTYPE html>
<html>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="card" style="max-width: 400px; margin: 50px auto;">
            <div class="card-body">
                <h4 class="text-center mb-4">🔐 Quiz System Login</h4>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="email" name="email" placeholder="Email" 
                           class="form-control mb-2" 
                           value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''; ?>" required>
                    
                    <input type="password" name="password" placeholder="Password" 
                           class="form-control mb-2"
                           value="<?php echo isset($_COOKIE['user_password']) ? htmlspecialchars($_COOKIE['user_password']) : ''; ?>" required>
                    
                    <div class="mb-2">
                        <input type="checkbox" name="remember" id="remember" 
                               <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
                        <label for="remember">Remember Me</label>
                    </div>
                    
                    <input type="submit" name="login" value="Login" class="btn btn-primary w-100">
                    
                    <div class="text-center mt-2">
                        <a href="Register.php" class="btn btn-success w-100">Register New Account</a>
                    </div>
                    
                    <div class="text-center mt-2">
                        <a href="forgot_password.php" class="text-decoration-none">
                            <i class="fas fa-key"></i> Forgot Password?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>