<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: index.php");
    exit();
}

$quizID = $_GET['id'];
$error = '';
$success = '';

// Get quiz data!!!!!
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quizzes WHERE quizID='$quizID'"));

if (!$quiz) {
    header("Location: ManageQuiz.php");
    exit();
}

if(isset($_POST['update']))
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
        $sql = "UPDATE quizzes SET quizTitle='$title', description='$description', timeLimit='$timeLimit' 
                WHERE quizID='$quizID'";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Quiz Updated Successfully!";
            // Refresh data
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
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-warning">
                <h4>Edit Quiz</h4>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <label>Quiz Title</label>
                    <input type="text" name="title" class="form-control mb-3" 
                           value="<?php echo htmlspecialchars($quiz['quizTitle']); ?>" required>
                    
                    <label>Description</label>
                    <textarea name="description" class="form-control mb-3" required><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                    
                    <label>Time Limit (Minutes)</label>
                    <input type="number" name="timeLimit" class="form-control mb-3" 
                           value="<?php echo $quiz['timeLimit']; ?>" min="1" required>
                    
                    <button type="submit" name="update" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Quiz
                    </button>
                    <a href="ManageQuiz.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>