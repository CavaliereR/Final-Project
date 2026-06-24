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
    <title>Logout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
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