<?php
session_start();


if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
 
    session_destroy();
    
    setcookie('user_email', '', time() - 3600, "/");
    setcookie('user_password', '', time() - 3600, "/");
    
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card text-center" style="max-width: 400px; margin: 100px auto;">
            <div class="card-body">
                <i class="fas fa-sign-out-alt fa-3x text-warning mb-3"></i>
                <h4>Confirm Logout</h4>
                <p>Are you sure you want to logout?</p>
                <a href="logout.php?confirm=yes" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Yes, Logout
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </div>
</body>
</html>