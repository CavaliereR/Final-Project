<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$studentID = $_SESSION['userID'];
$quizID = $_POST['quizID'];

// Debug array
$debug = [];
$debug[] = "=== DEBUG INFO ===";
$debug[] = "Student ID: $studentID";
$debug[] = "Quiz ID: $quizID";
$debug[] = "Files received: " . print_r($_FILES, true);
$debug[] = "POST data: " . print_r($_POST, true);

// Function to handle file uploads
function uploadFile($file, $questionID, $studentID, $quizID, &$debug) {
    $target_dir = "uploads/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
        $debug[] = "Created main uploads directory";
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowed_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "txt");
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $debug[] = "Invalid file type: $file_extension";
        return ["error" => "Invalid file type."];
    }
    
    if ($file["size"] > 5000000) {
        $debug[] = "File too large: " . $file["size"];
        return ["error" => "File is too large. Maximum size is 5MB."];
    }
    
    $student_dir = $target_dir . "student_" . $studentID . "/";
    if (!file_exists($student_dir)) {
        mkdir($student_dir, 0777, true);
        $debug[] = "Created student directory: $student_dir";
    }
    
    $quiz_dir = $student_dir . "quiz_" . $quizID . "/";
    if (!file_exists($quiz_dir)) {
        mkdir($quiz_dir, 0777, true);
        $debug[] = "Created quiz directory: $quiz_dir";
    }
    
    $new_filename = "q" . $questionID . "_" . time() . "_" . basename($file["name"]);
    $target_file = $quiz_dir . $new_filename;
    $relative_path = "uploads/student_" . $studentID . "/quiz_" . $quizID . "/" . $new_filename;
    
    $debug[] = "Target file: $target_file";
    $debug[] = "Relative path: $relative_path";
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $debug[] = "File moved successfully!";
        return ["success" => $relative_path];
    } else {
        $debug[] = "Failed to move file. Check permissions.";
        $debug[] = "Temp file: " . $file["tmp_name"];
        $debug[] = "Target: $target_file";
        return ["error" => "Error uploading file."];
    }
}

$score = 0;
$total = 0;

// First, insert the result to get a resultID
$resultQuery = "INSERT INTO results (studentID, quizID, score) VALUES ('$studentID', '$quizID', 0)";
mysqli_query($conn, $resultQuery);
$resultID = mysqli_insert_id($conn);
$debug[] = "Result ID: $resultID";

// Get all questions for this quiz
$questionsQuery = mysqli_query($conn, "SELECT questionID, question_type FROM questions WHERE quizID='$quizID'");
if (!$questionsQuery) {
    die("Error fetching questions: " . mysqli_error($conn));
}

while ($qRow = mysqli_fetch_assoc($questionsQuery)) {
    $questionID = $qRow['questionID'];
    $questionType = $qRow['question_type'];
    $debug[] = "Processing question $questionID, type: $questionType";
    $total++;
    $isCorrect = 0;
    $studentAnswer = 'Not answered';
    
    if ($questionType == 'file') {
        $fileKey = 'file_' . $questionID;
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === 0) {
            $debug[] = "File received for question $questionID: " . $_FILES[$fileKey]['name'];
            $uploadResult = uploadFile($_FILES[$fileKey], $questionID, $studentID, $quizID, $debug);
            if (isset($uploadResult['success'])) {
                $score++;
                $isCorrect = 1;
                $filePath = $uploadResult['success'];
                $studentAnswer = $filePath;
                
                mysqli_query($conn, "INSERT INTO submissions (studentID, questionID, quizID, filePath) 
                                    VALUES ('$studentID', '$questionID', '$quizID', '$filePath')");
                $debug[] = "Saved to submissions: $filePath";
            } else {
                $debug[] = "Upload failed: " . $uploadResult['error'];
            }
        } else {
            $debug[] = "No file found for question $questionID. File key: $fileKey, error: " . ($_FILES[$fileKey]['error'] ?? 'not set');
        }
    } else { // MCQ
        // Check if answer exists in POST
        if (isset($_POST['answer'][$questionID])) {
            $answer = $_POST['answer'][$questionID];
            $studentAnswer = mysqli_real_escape_string($conn, $answer);
            $sql = "SELECT answer FROM questions WHERE questionID='$questionID'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                if ($row && $answer == $row['answer']) {
                    $score++;
                    $isCorrect = 1;
                }
                $debug[] = "MCQ answer for $questionID: $answer, correct: " . ($isCorrect ? 'yes' : 'no');
            }
        } else {
            $debug[] = "No answer provided for MCQ question $questionID";
            $studentAnswer = 'Not answered';
        }
    }
    
    // Save student's answer to student_answers table
    $insertAnswer = "INSERT INTO student_answers (resultID, questionID, studentID, quizID, student_answer, is_correct) 
                     VALUES ('$resultID', '$questionID', '$studentID', '$quizID', '$studentAnswer', '$isCorrect')";
    mysqli_query($conn, $insertAnswer);
}

// Update the score in results
mysqli_query($conn, "UPDATE results SET score = '$score' WHERE resultID = '$resultID'");
$debug[] = "Final score: $score / $total";
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; min-height: 100vh; display: flex; align-items: center; }
        .result-container { max-width: 600px; width: 100%; margin: 0 auto; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header { background: #8B0000 !important; color: white !important; text-align: center; padding: 20px; border: none; }
        .card-header h2 { margin: 0; font-weight: 600; }
        .card-body { padding: 30px; text-align: center; }
        .btn-primary, .btn-info, .btn-warning { border-radius: 10px; padding: 12px 25px; font-weight: 600; transition: all 0.3s ease; margin: 5px; }
        .btn-primary { background: #8B0000 !important; border-color: #8B0000 !important; }
        .btn-primary:hover { background: #6d0000 !important; border-color: #6d0000 !important; }
        .btn-info { background: #17a2b8 !important; border-color: #17a2b8 !important; }
        .btn-warning { background: #ffc107 !important; border-color: #ffc107 !important; color: #212529; }
        .alert { border-radius: 10px; }
        .debug-box { background: #f0f0f0; padding: 15px; border-radius: 10px; text-align: left; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto; white-space: pre-wrap; margin-top: 15px; }
        .score-display { font-size: 48px; font-weight: 700; color: #8B0000; }
        .score-label { font-size: 18px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-check-circle me-2"></i> Quiz Submitted!</h2>
                </div>
                <div class="card-body">
                    <div>
                        <span class="score-label">Your Score</span>
                        <div class="score-display"><?php echo $score; ?> / <?php echo $total; ?></div>
                    </div>
                    <?php if ($score == $total && $total > 0): ?>
                        <div class="alert alert-success mt-3"><i class="fas fa-star me-2"></i> Perfect Score! 🎉</div>
                    <?php elseif ($score >= $total/2 && $total > 0): ?>
                        <div class="alert alert-info mt-3"><i class="fas fa-thumbs-up me-2"></i> Good job! 👍</div>
                    <?php elseif ($total > 0): ?>
                        <div class="alert alert-warning mt-3"><i class="fas fa-graduation-cap me-2"></i> Keep practicing! 💪</div>
                    <?php endif; ?>
                    
                    <!-- DEBUG OUTPUT -->
                    <div class="debug-box">
                        <?php foreach ($debug as $line) { echo htmlspecialchars($line) . "\n"; } ?>
                    </div>
                    
                    <div class="mt-4">
                        <a href="StudentDashboard.php" class="btn btn-primary"><i class="fas fa-home"></i> Dashboard</a>
                        <a href="QuizHistory.php" class="btn btn-info"><i class="fas fa-history"></i> Quiz History</a>
                        <a href="Leaderboard.php" class="btn btn-warning"><i class="fas fa-trophy"></i> Leaderboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>