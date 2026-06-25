<?php
session_start();
include("Database.php");


if (!isset($
_SESSION['userID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: index.php");
    exit();
}

$quizID = isset($_GET['id']) ? $_GET['id'] : 0;
$error = '';
$success = '';

// Check if quiz exists
$quizQuery = mysqli_query($conn, "SELECT * FROM quizzes WHERE quizID='$quizID'");
if (!$quizQuery) {
    die("Query failed: " . mysqli_error($conn));
}

$quiz = mysqli_fetch_assoc($quizQuery);

if (!$quiz) {
    header("Location: ManageQuiz.php");
    exit();
}

if(isset($_POST['update']))
{
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $timeLimit = (int)$_POST['timeLimit'];
    
    if(empty($title)) {
        $error = "Quiz title is required.";
    } elseif(empty($description)) {
        $error = "Quiz description is required.";
    } elseif($timeLimit < 1) {
        $error = "Time limit must be at least 1 minute.";
    } else {
        $sql = "UPDATE quizzes SET quizTitle='$title', description='$description', timeLimit='$timeLimit' 
                WHERE quizID='$quizID'";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Quiz Updated Successfully!";
            // Refresh quiz data
            $quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quizzes WHERE quizID='$quizID'"));
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>
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
            background: #ffc107 !important;
            color: #212529 !important;
            padding: 20px;
            border: none;
        }
        .card-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
        }
        .btn-warning, .btn-secondary {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-warning {
            background: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529;
        }
        .btn-warning:hover {
            background: #e0a800 !important;
            border-color: #e0a800 !important;
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
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.15);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-edit me-2"></i> Edit Quiz</h4>
                </div>
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quiz Title</label>
                            <input type="text" name="title" class="form-control" 
                                value="<?php echo htmlspecialchars($quiz['quizTitle']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Time Limit (Minutes)</label>
                            <input type="number" name="timeLimit" class="form-control" 
                                value="<?php echo $quiz['timeLimit']; ?>" min="1" required>
                        </div>
                        
                        <button type="submit" name="update" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Quiz
                        </button>
                        <a href="ManageQuiz.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>