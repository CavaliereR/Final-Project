<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$resultID = $_GET['id'];


$result = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT r.*, q.quizTitle, q.quizID
    FROM results r
    JOIN quizzes q ON r.quizID = q.quizID
    WHERE r.resultID = '$resultID' AND r.studentID = '" . $_SESSION['userID'] . "'
"));

if (!$result) {
    header("Location: QuizHistory.php");
    exit();
}


$questions = mysqli_query($conn, "SELECT * FROM questions WHERE quizID = '" . $result['quizID'] . "'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Answers - <?php echo htmlspecialchars($result['quizTitle']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2><?php echo htmlspecialchars($result['quizTitle']); ?></h2>
        <p>Score: <?php echo $result['score']; ?></p>
        <hr>
        
        <?php $num = 1; while($q = mysqli_fetch_assoc($questions)): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5><?php echo $num . ". " . htmlspecialchars($q['questionText']); ?></h5>
                    
                    <?php if($q['question_type'] == 'file'): ?>
                        <p><strong>File Upload Question</strong></p>
                        <?php
                   
                        $submission = mysqli_fetch_assoc(mysqli_query($conn, 
                            "SELECT * FROM submissions WHERE studentID = '" . $_SESSION['userID'] . "' 
                             AND questionID = '" . $q['questionID'] . "' AND quizID = '" . $result['quizID'] . "'"
                        ));
                        if($submission): ?>
                            <a href="<?php echo $submission['filePath']; ?>" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-file"></i> View Uploaded File
                            </a>
                            <p><small>Uploaded: <?php echo $submission['submitted_at']; ?></small></p>
                        <?php else: ?>
                            <p class="text-muted">No file uploaded</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p><strong>Answer:</strong> <?php echo htmlspecialchars($q['answer']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php $num++; endwhile; ?>
        
        <a href="QuizHistory.php" class="btn btn-secondary">Back to History</a>
        <a href="StudentDashboard.php" class="btn btn-primary">Dashboard</a>
    </div>
</body>
</html>