<?php
session_start();
include("config.php");

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['save']))
{
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $timeLimit = (int)$_POST['timeLimit'];
    

    if(empty($title)) {
        $error = "Quiz title is required.";
    } elseif(empty($description)) {
        $error = "Quiz description is required.";
    } elseif($timeLimit < 1) {
        $error = "Time limit must be at least 1 minute.";
    } else {
        $sql = "INSERT INTO quizzes (quizTitle, description, timeLimit) 
                VALUES ('$title','$description','$timeLimit')";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Quiz Created Successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
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
            padding: 20px;
            border: none;
        }
        .card-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
        }
        .btn-primary, .btn-success, .btn-secondary, .btn-info {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-primary {
            background: #8B0000 !important;
            border-color: #8B0000 !important;
        }
        .btn-primary:hover {
            background: #6d0000 !important;
            border-color: #6d0000 !important;
        }
        .btn-success {
            background: #28a745 !important;
            border-color: #28a745 !important;
        }
        .btn-success:hover {
            background: #218838 !important;
            border-color: #218838 !important;
        }
        .btn-secondary {
            background: #6c757d !important;
            border-color: #6c757d !important;
        }
        .btn-secondary:hover {
            background: #5a6268 !important;
            border-color: #5a6268 !important;
        }
        .btn-info {
            background: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }
        .btn-info:hover {
            background: #138496 !important;
            border-color: #138496 !important;
        }
        .btn i {
            margin-right: 8px;
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
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-plus-circle me-2"></i> Create Quiz</h2>
                </div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alertdanger - CreateQuiz.php:144"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alertsuccess - CreateQuiz.php:147"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quiz Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter quiz title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Enter quiz description" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Time Limit (Minutes)</label>
                            <input type="number" name="timeLimit" class="form-control" placeholder="Enter time limit" min="1" required>
                        </div>
                        
                        <button type="submit" name="save" class="btn btn-success">
                            <i class="fas fa-save"></i> Create Quiz
                        </button>
                    </form>
                    
                    <div class="action-buttons">
                        <a href="TeacherDashboard.php" class="btn btn-secondary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a href="ManageQuiz.php" class="btn btn-info">
                            <i class="fas fa-tasks"></i> Manage Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>