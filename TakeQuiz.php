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
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><?php echo htmlspecialchars($quiz['quizTitle']); ?></h2>
            <p><?php echo htmlspecialchars($quiz['description']); ?></p>
        </div>
        <div>
            <h4 id="timer"></h4>
            <p class="text-muted">Time Remaining</p>
        </div>
    </div>
    
    <a href="StudentDashboard.php" class="btn btn-secondary mb-3">
        <i class="fas fa-times"></i> Cancel Quiz
    </a>
    
    <form action="SubmitQuiz.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="quizID" value="<?php echo $quizID; ?>">

        <?php
        $number = 1;
        while ($row = mysqli_fetch_assoc($questions)) {
        ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5>
                        <?php echo $number . ". " . htmlspecialchars($row['questionText']); ?>
                        <span class="required-star">*</span>
                    </h5>

                    <?php if ($row['question_type'] == 'file'): ?>
                        <!-- File Upload Question -->
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
                        <!-- Multiple Choice Question -->
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
            </div>
        <?php
            $number++;
        }
        ?>

        <input type="submit" value="Submit Quiz" class="btn btn-success">
    </form>
</div>

<script>
    // Timer functionality
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