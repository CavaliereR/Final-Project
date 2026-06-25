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
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .logout-container {
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
        .btn-danger, .btn-secondary {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-danger {
            background: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        .btn-danger:hover {
            background: #c82333 !important;
            border-color: #c82333 !important;
        }
        .btn-secondary {
            background: #6c757d !important;
            border-color: #6c757d !important;
        }
        .btn-secondary:hover {
            background: #5a6268 !important;
            border-color: #5a6268 !important;
        }
        .btn i {
            margin-right: 8px;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: #f8f9fa;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .icon-circle i {
            font-size: 40px;
            color: #ffc107;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout-container">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-sign-out-alt me-2"></i> Confirm Logout</h4>
                </div>
                <div class="card-body">
                    <div class="icon-circle">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h5>Are you sure you want to logout?</h5>
                    
                    <div class="action-buttons">
                        <a href="logout.php?confirm=yes" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Yes, Logout
                        </a>
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>