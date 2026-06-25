<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$quizID = $_GET['id'];

$quiz = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM quizzes WHERE quizID='$quizID'")
);

$questions = mysqli_query($conn, "SELECT * FROM questions WHERE quizID='$quizID'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .quiz-container {
            max-width: 800px;
            margin: 30px auto;
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
        .btn-primary, .btn-success, .btn-secondary {
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
        .btn i {
            margin-right: 8px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #8B0000;
            box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.15);
        }
        .file-upload-box {
            border: 2px dashed #ccc;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .file-upload-box:hover {
            border-color: #28a745;
            background-color: #f0f8ff;
        }
        .file-upload-box i {
            font-size: 48px;
            color: #6c757d;
        }
        .required-star {
            color: red;
        }
        .upload-info {
            font-size: 12px;
            color: #6c757d;
        }
        #timer {
            color: #dc3545;
            font-weight: bold;
            font-size: 24px;
        }
        .form-check-input:checked {
            background-color: #8B0000;
            border-color: #8B0000;
        }
        .question-card {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            transition: all 0.3s ease;
        }
        .question-card:hover {
            border-color: #8B0000;
        }
        .timer-box {
            background: #f8f9fa;
            padding: 15px 25px;
            border-radius: 15px;
            text-align: center;
        }
        .timer-box h4 {
            margin: 0;
            color: #dc3545;
        }
        .timer-box p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .quiz-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .cancel-btn {
            background: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white;
        }
        .cancel-btn:hover {
            background: #c82333 !important;
            border-color: #c82333 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="quiz-container">
            <div class="card">
                <div class="card-header">
                    <div class="quiz-header">
                        <div>
                            <h2><i class="fas fa-pencil-alt me-2"></i> <?php echo htmlspecialchars($quiz['quizTitle']); ?></h2>
                            <p class="text-white-50 mb-0"><?php echo htmlspecialchars($quiz['description']); ?></p>
                        </div>
                        <div class="timer-box">
                            <h4 id="timer"></h4>
                            <p>Time Remaining</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <a href="StudentDashboard.php" class="btn cancel-btn">
                        <i class="fas fa-times"></i> Cancel Quiz
                    </a>
                    
                    <form action="SubmitQuiz.php" method="POST" enctype="multipart/form-data" class="mt-3">
                        <input type="hidden" name="quizID" value="<?php echo $quizID; ?>">

                        <?php
                        $number = 1;
                        while ($row = mysqli_fetch_assoc($questions)) {
                        ?>
                            <div class="question-card">
                                <h5>
                                    <?php echo $number . ". " . htmlspecialchars($row['questionText']); ?>
                                    <span class="required-star">*</span>
                                </h5>

                                <?php if ($row['question_type'] == 'file'): ?>
                                    <div class="file-upload-box">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mt-2">Upload your file for this question</p>
                                        <input type="file" 
                                            class="form-control" 
                                            name="file_<?php echo $row['questionID']; ?>" 
                                            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                        <small class="upload-info">
                                            Allowed: JPG, PNG, GIF, PDF, DOC, TXT (Max 5MB)
                                        </small>
                                    </div>
                                    <input type="hidden" name="question_type_<?php echo $row['questionID']; ?>" value="file">
                                    
                                <?php else: ?>
                                    <?php
                                    $choices = [
                                        'choiceA' => $row['choiceA'],
                                        'choiceB' => $row['choiceB'],
                                        'choiceC' => $row['choiceC'],
                                        'choiceD' => $row['choiceD']
                                    ];
                                    foreach ($choices as $key => $choice):
                                        if (!empty($choice)):
                                    ?>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                type="radio" 
                                                name="answer[<?php echo $row['questionID']; ?>]" 
                                                value="<?php echo htmlspecialchars($choice); ?>"
                                                id="q<?php echo $row['questionID']; ?>_<?php echo $key; ?>">
                                            <label class="form-check-label" for="q<?php echo $row['questionID']; ?>_<?php echo $key; ?>">
                                                <?php echo htmlspecialchars($choice); ?>
                                            </label>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                    <input type="hidden" name="question_type_<?php echo $row['questionID']; ?>" value="mcq">
                                <?php endif; ?>
                            </div>
                        <?php
                            $number++;
                        }
                        ?>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Submit Quiz
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let time = <?php echo $quiz['timeLimit'] * 60; ?>;
        
        setInterval(function() {
            let min = Math.floor(time / 60);
            let sec = time % 60;
            document.getElementById("timer").innerHTML = 
                String(min).padStart(2, '0') + ":" + String(sec).padStart(2, '0');
            time--;
            
            if (time < 0) {
                document.getElementById("timer").innerHTML = "Time's Up!";
                document.querySelector("form").submit();
            }
        }, 1000);
    </script>
</body>
</html>