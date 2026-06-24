<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$studentID = $_SESSION['userID'];
$quizID = $_POST['quizID'];


function uploadFile($file, $questionID, $studentID, $quizID) {
    $target_dir = "uploads/";
    

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowed_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "txt");
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return ["error" => "Invalid file type. Only JPG, PNG, GIF, PDF, DOC, and TXT files are allowed."];
    }
    
    if ($file["size"] > 5000000) {
        return ["error" => "File is too large. Maximum size is 5MB."];
    }
    
    $new_filename = "q" . $questionID . "_student" . $studentID . "_" . time() . "_" . basename($file["name"]);
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => $target_file];
    } else {
        return ["error" => "Error uploading file."];
    }
}

$score = 0;
$total = 0;


foreach ($_POST['answer'] as $questionID => $answer) {
    $total++;
    
 
    $questionType = isset($_POST['question_type_' . $questionID]) ? $_POST['question_type_' . $questionID] : 'mcq';
    
    if ($questionType == 'file') {
 
        $fileKey = 'file_' . $questionID;
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
            $uploadResult = uploadFile($_FILES[$fileKey], $questionID, $studentID, $quizID);
            if (isset($uploadResult['success'])) {
             
                $score++;
                $filePath = $uploadResult['success'];
                
               
                mysqli_query($conn, "INSERT INTO submissions (studentID, questionID, quizID, filePath) 
                                     VALUES ('$studentID', '$questionID', '$quizID', '$filePath')");
            }
        }
    } else {
   
        $sql = "SELECT answer FROM questions WHERE questionID='$questionID'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        
        if ($answer == $row['answer']) {
            $score++;
        }
    }
}


mysqli_query($conn, "INSERT INTO results (studentID, quizID, score) VALUES ('$studentID', '$quizID', '$score')");
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body text-center">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-3x"></i>
                    <h2 class="mt-3">Quiz Submitted!</h2>
                    <h3>
                        Score: <?php echo $score; ?> / <?php echo $total; ?>
                    </h3>
                    <?php if ($score == $total): ?>
                        <div class="alert alert-success">Perfect Score! 🎉</div>
                    <?php elseif ($score >= $total/2): ?>
                        <div class="alert alert-info">Good job! 👍</div>
                    <?php else: ?>
                        <div class="alert alert-warning">Keep practicing! 💪</div>
                    <?php endif; ?>
                </div>
                
                <a href="StudentDashboard.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="QuizHistory.php" class="btn btn-info">
                    <i class="fas fa-history"></i> Quiz History
                </a>
                <a href="Leaderboard.php" class="btn btn-warning">
                    <i class="fas fa-trophy"></i> Leaderboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>