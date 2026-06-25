<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$resultID = isset($_GET['id']) ? $_GET['id'] : 0;

// Get the result and quiz details
$resultQuery = mysqli_query($conn, "
    SELECT r.*, q.quizTitle, q.quizID
    FROM results r
    JOIN quizzes q ON r.quizID = q.quizID
    WHERE r.resultID = '$resultID' AND r.studentID = '" . $_SESSION['userID'] . "'
");

if (!$resultQuery) {
    die("Query failed: " . mysqli_error($conn));
}

$result = mysqli_fetch_assoc($resultQuery);

if (!$result) {
    header("Location: QuizHistory.php");
    exit();
}

// Get questions for this quiz
$questions = mysqli_query($conn, "SELECT * FROM questions WHERE quizID = '" . $result['quizID'] . "'");
if (!$questions) {
    die("Questions query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Answers - <?php echo htmlspecialchars($result['quizTitle']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .answers-container {
            max-width: 800px;
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
        .btn-primary, .btn-secondary {
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
        .question-card {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
        }
        .question-card h5 {
            color: #333;
            font-weight: 600;
        }
        .answer-box {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .answer-box strong {
            color: #8B0000;
        }
        .badge-success {
            background: #28a745;
            color: white;
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
        <div class="answers-container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-alt me-2"></i> <?php echo htmlspecialchars($result['quizTitle']); ?></h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-star me-2"></i> Score: <strong><?php echo $result['score']; ?></strong>
                    </div>
                    
                    <?php 
                    $num = 1; 
                    if (mysqli_num_rows($questions) > 0) {
                        while($q = mysqli_fetch_assoc($questions)): 
                    ?>
                        <div class="question-card">
                            <h5><?php echo $num . ". " . htmlspecialchars($q['questionText']); ?></h5>
                            
                            <?php if($q['question_type'] == 'file'): ?>
                                <div class="answer-box">
                                    <i class="fas fa-upload me-2"></i> <strong>File Upload Question</strong>
                                    <?php
                                    $submission = mysqli_fetch_assoc(mysqli_query($conn, 
                                        "SELECT * FROM submissions WHERE studentID = '" . $_SESSION['userID'] . "' 
                                        AND questionID = '" . $q['questionID'] . "' AND quizID = '" . $result['quizID'] . "'"
                                    ));
                                    if($submission): ?>
                                        <br>
                                        <a href="<?php echo $submission['filePath']; ?>" class="btn btn-primary btn-sm mt-2" target="_blank">
                                            <i class="fas fa-file"></i> View Uploaded File
                                        </a>
                                        <p><small class="text-muted">Uploaded: <?php echo $submission['submitted_at']; ?></small></p>
                                    <?php else: ?>
                                        <p class="text-muted mt-2">No file uploaded</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="answer-box">
                                    <i class="fas fa-check-circle me-2" style="color: #28a745;"></i>
                                    <strong>Answer:</strong> <?php echo htmlspecialchars($q['answer']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                        $num++; 
                        endwhile;
                    } else {
                    ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No questions found for this quiz.
                        </div>
                    <?php } ?>
                    
                    <div class="action-buttons">
                        <a href="QuizHistory.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to History
                        </a>
                        <a href="StudentDashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>