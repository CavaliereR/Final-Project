<?php
include("Database.php");

// Check if we're viewing a specific result
$viewResultID = isset($_GET['view']) ? $_GET['view'] : 0;

// If viewing a specific result, show the answers
if ($viewResultID > 0) {
    // Get the result details
    $resultQuery = mysqli_query($conn, "
        SELECT r.*, u.fullname, q.quizTitle 
        FROM results r
        INNER JOIN users u ON u.userID = r.studentID
        INNER JOIN quizzes q ON q.quizID = r.quizID
        WHERE r.resultID = '$viewResultID'
    ");
    $viewResult = mysqli_fetch_assoc($resultQuery);
    
    if (!$viewResult) {
        header("Location: ViewResults.php");
        exit();
    }
    
    // Get the questions for this quiz WITH student answers
    $questions = mysqli_query($conn, "
        SELECT q.*, sa.student_answer, sa.is_correct 
        FROM questions q
        LEFT JOIN student_answers sa ON q.questionID = sa.questionID 
            AND sa.resultID = '$viewResultID' AND sa.studentID = '" . $viewResult['studentID'] . "'
        WHERE q.quizID = '" . $viewResult['quizID'] . "'
    ");
    
    // Get submissions for file uploads (for download links)
    $submissions = mysqli_query($conn, "SELECT * FROM submissions WHERE studentID = '" . $viewResult['studentID'] . "' AND quizID = '" . $viewResult['quizID'] . "'");
    $submissionMap = array();
    while ($sub = mysqli_fetch_assoc($submissions)) {
        $submissionMap[$sub['questionID']] = $sub;
    }
}

// Main results query
$sql = "SELECT
            results.*,
            users.fullname,
            quizzes.quizTitle
        FROM results
        INNER JOIN users ON users.userID = results.studentID
        INNER JOIN quizzes ON quizzes.quizID = results.quizID
        ORDER BY results.dateTaken DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .results-container {
            max-width: 1100px;
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
        .btn-primary, .btn-info, .btn-success, .btn-secondary {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 3px;
        }
        .btn-primary {
            background: #8B0000 !important;
            border-color: #8B0000 !important;
        }
        .btn-primary:hover {
            background: #6d0000 !important;
            border-color: #6d0000 !important;
        }
        .btn-info {
            background: #17a2b8 !important;
            border-color: #17a2b8 !important;
            color: white;
        }
        .btn-info:hover {
            background: #138496 !important;
            border-color: #138496 !important;
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
        .btn i {
            margin-right: 6px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: #8B0000 !important;
            color: white !important;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        .no-results {
            text-align: center;
            padding: 40px 0;
            color: #6c757d;
        }
        .no-results i {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .answer-card {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }
        .answer-card h5 {
            color: #333;
            font-weight: 600;
        }
        .answer-box {
            background: white;
            padding: 12px 18px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #8B0000;
        }
        .answer-box.correct {
            border-left-color: #28a745;
            background: #f0fff4;
        }
        .answer-box.incorrect {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .file-preview-card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
            text-align: center;
        }
        .file-preview-card img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .file-preview-card .file-icon {
            font-size: 64px;
            color: #6c757d;
        }
        .file-preview-card .file-name {
            margin: 10px 0;
            font-weight: 500;
        }
        .back-link {
            margin-bottom: 20px;
            display: inline-block;
        }
        .badge-correct {
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-incorrect {
            background: #dc3545;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="results-container">
            
            <?php if ($viewResultID > 0 && isset($viewResult)) { ?>
                <!-- View Answers Mode -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-eye me-2"></i> <?php echo htmlspecialchars($viewResult['fullname']); ?>'s Answers</h2>
                    </div>
                    <div class="card-body">
                        <div class="back-link">
                            <a href="ViewResults.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Results
                            </a>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Quiz:</strong> <?php echo htmlspecialchars($viewResult['quizTitle']); ?> &nbsp;|&nbsp;
                            <strong>Score:</strong> <?php echo $viewResult['score']; ?>
                        </div>
                        
                        <?php 
                        $num = 1;
                        if (mysqli_num_rows($questions) > 0) {
                            while($q = mysqli_fetch_assoc($questions)) { 
                                $isCorrect = isset($q['is_correct']) ? $q['is_correct'] : 0;
                                $studentAnswer = isset($q['student_answer']) ? $q['student_answer'] : 'Not answered';
                                $answerClass = '';
                                if ($studentAnswer != 'Not answered') {
                                    $answerClass = $isCorrect ? 'correct' : 'incorrect';
                                }
                        ?>
                            <div class="answer-card">
                                <h5><?php echo $num . ". " . htmlspecialchars($q['questionText']); ?></h5>
                                
                                <?php if($q['question_type'] == 'file') { ?>
                                    <div class="answer-box <?php echo $answerClass; ?>">
                                        <i class="fas fa-upload me-2" style="color: #17a2b8;"></i>
                                        <strong>File Upload Question</strong>
                                        <?php if (isset($submissionMap[$q['questionID']])) { 
                                            $sub = $submissionMap[$q['questionID']];
                                            $filePath = $sub['filePath'];
                                            $fileName = basename($filePath);
                                            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                            $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg');
                                            
                                            if (file_exists($filePath)) { ?>
                                            <div class="file-preview-card">
                                                <?php if (in_array($fileExtension, $imageExtensions)) { ?>
                                                    <img src="<?php echo $filePath; ?>" alt="Student Upload">
                                                <?php } else { ?>
                                                    <i class="fas fa-file file-icon"></i>
                                                    <div class="file-name"><?php echo htmlspecialchars($fileName); ?></div>
                                                <?php } ?>
                                                <br>
                                                <a href="<?php echo $filePath; ?>" class="btn btn-success btn-sm" download>
                                                    <i class="fas fa-download"></i> Download File
                                                </a>
                                                <p><small class="text-muted">Uploaded: <?php echo $sub['submitted_at']; ?></small></p>
                                            </div>
                                        <?php 
                                            } else { ?>
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                File not found on server.
                                            </div>
                                        <?php 
                                            }
                                        } else { ?>
                                            <p class="text-muted mt-2">No file uploaded for this question</p>
                                        <?php } ?>
                                        <?php if ($studentAnswer != 'Not answered') { ?>
                                            <br>
                                            <span class="<?php echo $isCorrect ? 'badge-correct' : 'badge-incorrect'; ?>">
                                                <?php echo $isCorrect ? '✓ Correct' : '✗ Incorrect'; ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="answer-box <?php echo $answerClass; ?>">
                                        <i class="fas fa-user-edit me-2" style="color: #6c757d;"></i>
                                        <strong>Student's Answer:</strong> <?php echo htmlspecialchars($studentAnswer); ?>
                                        <?php if ($studentAnswer != 'Not answered') { ?>
                                            <br>
                                            <span class="<?php echo $isCorrect ? 'badge-correct' : 'badge-incorrect'; ?>">
                                                <?php echo $isCorrect ? '✓ Correct' : '✗ Incorrect'; ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle" style="color: #28a745;"></i> 
                                            Correct Answer: <?php echo htmlspecialchars($q['answer']); ?>
                                        </small>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php 
                            $num++; 
                            } // end while
                        } else { ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> No questions found for this quiz.
                            </div>
                        <?php } ?>
                        
                        <a href="ViewResults.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Results
                        </a>
                    </div>
                </div>
                
            <?php } else { ?>
                <!-- Main Results List -->
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-chart-bar me-2"></i> Student Results</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['quizTitle']); ?></td>
                                        <td><?php echo $row['score']; ?></td>
                                        <td><?php echo $row['dateTaken']; ?></td>
                                        <td>
                                            <a href="ViewResults.php?view=<?php echo $row['resultID']; ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View Answers
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        } // end while
                                    } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i> No results found yet.
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="TeacherDashboard.php" class="btn btn-primary">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                            <a href="ManageQuiz.php" class="btn btn-info">
                                <i class="fas fa-tasks"></i> Manage Quiz
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>