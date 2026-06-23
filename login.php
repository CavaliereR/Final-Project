<?php
session_start();
include("Database.php");

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result)>0)
    {
        $row = mysqli_fetch_assoc($result);

        if(password_verify($password,$row['password']))
        {
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['fullname'] = $row['fullname'];

            if($row['role']=="Teacher")
            {
                header("Location: TeacherDashboard.php");
            }
            else
            {
                header("Location: StudentDashboard.php");
            }
        }
        else
        {
            echo "Invalid Password";
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
    .login-container {
        max-width: 400px;
        margin: 100px auto;
        padding: 30px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .btn-forgot {
        color: #6c757d;
        text-decoration: none;
        font-size: 14px;
    }
    .btn-forgot:hover {
        color: #8B0000;
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="container">
    <div class="login-container">
        <h3 class="text-center mb-4">University of the East</h3>
        
        <form method="POST">
            <input type="email"
                   name="email"
                   placeholder="Email"
                   class="form-control mb-2" required>

            <input type="password"
                   name="password"
                   placeholder="Password"
                   class="form-control mb-2" required>

            <input type="submit"
                   name="login"
                   value="Login"
                   class="btn btn-primary w-100">

            <div class="text-center mt-3">
                <a href="Register.php" class="btn btn-success w-100">
                    Register New Account
                </a>
            </div>
            
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="btn-forgot">
                    <i class="fas fa-key"></i> Forgot Password?
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>