<?php
session_start();

// Redirect if already logged in
if(isset($_SESSION['name']) && isset($_SESSION['role'])) {
    if($_SESSION['role'] == 'student') {
        header("Location: quiz.php");
        exit();
    } else if($_SESSION['role'] == 'teacher') {
        header("Location: interface.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University of the East</title>
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
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .school-header h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .school-header .subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .main-container {
            background-color: white;
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-top: 100px;
            width: 100%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .main-container h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .main-container .sub-text {
            color: #666;
            margin-bottom: 30px;
        }
        
        .role-card {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: #333;
            display: block;
            height: 100%;
        }
        
        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #8B0000;
        }
        
        .role-card i {
            font-size: 48px;
            color: #8B0000;
            margin-bottom: 15px;
        }
        
        .role-card h5 {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .role-card p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        
        .role-card.student:hover {
            border-color: #0066cc;
        }
        
        .role-card.student:hover i {
            color: #0066cc;
        }
        
        .role-card.teacher:hover {
            border-color: #28a745;
        }
        
        .role-card.teacher:hover i {
            color: #28a745;
        }
        
        .school-footer {
            text-align: center;
            color: #888;
            font-size: 13px;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 30px 20px;
                margin: 80px 15px 0 15px;
            }
            
            .school-header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <!-- School Header -->
    <div class="school-header text-center">
        <div class="container">
            <h1>🏫 University of the East</h1>
            <div class="subtitle">Excellence in Education</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="main-container">
            <div class="text-center">
                <h2>Welcome!</h2>
                <p class="sub-text">Please select your role to continue</p>
            </div>
            
            <div class="row g-4 mt-2">
                <!-- Student Card -->
                <div class="col-md-6">
                    <a href="login.php" class="role-card student">
                        <i class="fas fa-user-graduate"></i>
                        <h5>Student</h5>
                        <p>Take quizzes and track your progress</p>
                        <span class="badge bg-primary mt-2">Login</span>
                    </a>
                </div>
                
                <!-- Teacher Card -->
                <div class="col-md-6">
                    <a href="teacherlogin.php" class="role-card teacher">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h5>Teacher</h5>
                        <p>Create and manage quizzes</p>
                        <span class="badge bg-success mt-2">Login</span>
                    </a>
                </div>
            </div>
            
            <div class="school-footer mt-4">
                <i class="fas fa-shield-alt"></i> Secure Login System
            </div>
        </div>
    </div>

</body>
</html>