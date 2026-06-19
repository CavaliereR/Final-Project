<?php
session_start();

// Check if user is logged in as student
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Database connection
$connection = mysqli_connect("localhost", "root", "", "qez");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$student_name = $_SESSION['name'];
$student_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Function to handle file uploads
function uploadFile($file, $question_num) {
    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowed_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "txt");
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return "Invalid file type. Only JPG, PNG, GIF, PDF, DOC, and TXT files are allowed.";
    }
    
    if ($file["size"] > 5000000) { // 5MB limit
        return "File is too large. Maximum size is 5MB.";
    }
    
    $new_filename = "q" . $question_num . "_" . time() . "_" . basename($file["name"]);
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        return "Error uploading file.";
    }
}

// Check if quiz was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for unfinished quiz warning
    if (isset($_POST['confirm_incomplete']) && $_POST['confirm_incomplete'] === 'yes') {
        // User confirmed they want to submit incomplete quiz
        $force_submit = true;
    } else {
        $force_submit = false;
    }
    
    // Collect all answers
    $q1_text = isset($_POST['q1_text']) ? mysqli_real_escape_string($connection, trim($_POST['q1_text'])) : '';
    $q2_text = isset($_POST['q2_text']) ? mysqli_real_escape_string($connection, trim($_POST['q2_text'])) : '';
    $q3_text = isset($_POST['q3_text']) ? mysqli_real_escape_string($connection, trim($_POST['q3_text'])) : '';
    
    $q4_radio = isset($_POST['q4_radio']) ? mysqli_real_escape_string($connection, $_POST['q4_radio']) : '';
    $q5_radio = isset($_POST['q5_radio']) ? mysqli_real_escape_string($connection, $_POST['q5_radio']) : '';
    $q6_radio = isset($_POST['q6_radio']) ? mysqli_real_escape_string($connection, $_POST['q6_radio']) : '';
    $q7_radio = isset($_POST['q7_radio']) ? mysqli_real_escape_string($connection, $_POST['q7_radio']) : '';
    
    // Handle file uploads
    $q8_file = '';
    $q9_file = '';
    $q10_file = '';
    
    if (isset($_FILES['q8_file']) && $_FILES['q8_file']['error'] === 0) {
        $upload_result = uploadFile($_FILES['q8_file'], 8);
        if (strpos($upload_result, "Error") === false && strpos($upload_result, "Invalid") === false) {
            $q8_file = $upload_result;
        } else {
            $error = "Question 8: " . $upload_result;
        }
    }
    
    if (isset($_FILES['q9_file']) && $_FILES['q9_file']['error'] === 0) {
        $upload_result = uploadFile($_FILES['q9_file'], 9);
        if (strpos($upload_result, "Error") === false && strpos($upload_result, "Invalid") === false) {
            $q9_file = $upload_result;
        } else {
            $error = "Question 9: " . $upload_result;
        }
    }
    
    if (isset($_FILES['q10_file']) && $_FILES['q10_file']['error'] === 0) {
        $upload_result = uploadFile($_FILES['q10_file'], 10);
        if (strpos($upload_result, "Error") === false && strpos($upload_result, "Invalid") === false) {
            $q10_file = $upload_result;
        } else {
            $error = "Question 10: " . $upload_result;
        }
    }
    
    // Check if quiz is complete (all questions answered)
    $all_answered = true;
    $unanswered = array();
    
    if (empty($q1_text)) { $all_answered = false; $unanswered[] = "Question 1"; }
    if (empty($q2_text)) { $all_answered = false; $unanswered[] = "Question 2"; }
    if (empty($q3_text)) { $all_answered = false; $unanswered[] = "Question 3"; }
    if (empty($q4_radio)) { $all_answered = false; $unanswered[] = "Question 4"; }
    if (empty($q5_radio)) { $all_answered = false; $unanswered[] = "Question 5"; }
    if (empty($q6_radio)) { $all_answered = false; $unanswered[] = "Question 6"; }
    if (empty($q7_radio)) { $all_answered = false; $unanswered[] = "Question 7"; }
    if (empty($q8_file)) { $all_answered = false; $unanswered[] = "Question 8 (File Upload)"; }
    if (empty($q9_file)) { $all_answered = false; $unanswered[] = "Question 9 (File Upload)"; }
    if (empty($q10_file)) { $all_answered = false; $unanswered[] = "Question 10 (File Upload)"; }
    
    // If not complete and not forced, show warning
    if (!$all_answered && !$force_submit) {
        $incomplete_warning = true;
        $unanswered_list = implode(", ", $unanswered);
    } else {
        // Calculate score (only if complete or forced)
        $score = 0;
        
        // Check text answers (Questions 1-3)
        if ($q1_text == "correct_answer") $score += 10;
        if ($q2_text == "correct_answer") $score += 10;
        if ($q3_text == "correct_answer") $score += 10;
        
        // Check radio answers (Questions 4-7)
        if ($q4_radio == "correct") $score += 10;
        if ($q5_radio == "correct") $score += 10;
        if ($q6_radio == "correct") $score += 10;
        if ($q7_radio == "correct") $score += 10;
        
        // File upload questions get points for uploading (Questions 8-10)
        if (!empty($q8_file)) $score += 10;
        if (!empty($q9_file)) $score += 10;
        if (!empty($q10_file)) $score += 10;
        
        // Insert into database
        $query = "INSERT INTO quizzes (
            student_name, student_id, 
            q1_text, q2_text, q3_text,
            q4_radio, q5_radio, q6_radio, q7_radio,
            q8_file, q9_file, q10_file,
            score, is_completed
        ) VALUES (
            '$student_name', '$student_id',
            '$q1_text', '$q2_text', '$q3_text',
            '$q4_radio', '$q5_radio', '$q6_radio', '$q7_radio',
            '$q8_file', '$q9_file', '$q10_file',
            '$score', '" . ($all_answered ? '1' : '0') . "'
        )";
        
        if (mysqli_query($connection, $query)) {
            $message = "Quiz submitted successfully! Your score: $score/100";
            $submitted = true;
        } else {
            $error = "Error submitting quiz: " . mysqli_error($connection);
        }
    }
}

mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f0f0f0;
            min-height: 100vh;
        }
        
        .school-header {
            background-color: #8B0000;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        
        .quiz-container {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .question-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #8B0000;
        }
        
        .question-number {
            color: #8B0000;
            font-weight: bold;
            font-size: 18px;
        }
        
        .required-star {
            color: red;
        }
        
        .btn-submit {
            background-color: #8B0000;
            color: white;
            padding: 12px 40px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background-color: #6d0000;
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .radio-group {
            padding-left: 20px;
        }
        
        .radio-group label {
            margin-right: 20px;
        }
        
        .file-upload {
            border: 2px dashed #ccc;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .file-upload:hover {
            border-color: #8B0000;
        }
        
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .btn-logout-home {
            background-color: #6c757d;
            color: white;
            padding: 10px 30px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-logout-home:hover {
            background-color: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <!-- School Header -->
    <div class="school-header">
        <div class="container">
            <div class="d-flex align-items-center">
                <h4 class="flex-grow-1 text-center">📝 Quiz Time</h4>
                <span class="text-white me-3">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($student_name); ?>
                </span>
                <a href="?logout=1" class="btn btn-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Success/Error Messages -->
        <?php if (isset($message) && $message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <br><br>
                <a href="?logout=1" class="btn btn-logout-home">
                    <i class="fas fa-sign-out-alt"></i> Return to Home (Logout)
                </a>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Incomplete Warning -->
        <?php if (isset($incomplete_warning) && $incomplete_warning): ?>
            <div class="warning-box">
                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Incomplete Quiz</h5>
                <p>You have not answered the following questions:</p>
                <p><strong><?php echo htmlspecialchars($unanswered_list); ?></strong></p>
                <p>Are you sure you want to submit your quiz with incomplete answers?</p>
                <form method="post" action="quiz.php">
                    <input type="hidden" name="confirm_incomplete" value="yes">
                    <!-- Resubmit all form data -->
                    <?php foreach ($_POST as $key => $value): ?>
                        <?php if ($key !== 'confirm_incomplete'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" 
                                   value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check"></i> Yes, Submit Incomplete
                    </button>
                    <a href="quiz.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> No, Continue Editing
                    </a>
                </form>
            </div>
        <?php endif; ?>

        <!-- Quiz Form -->
        <?php if (!isset($submitted) || !$submitted): ?>
        <div class="quiz-container">
            <h3>Student Quiz</h3>
            <p class="text-muted">Answer all questions to complete the quiz. Questions 8-10 require file uploads.</p>
            <hr>

            <form method="post" action="quiz.php" enctype="multipart/form-data">
                
                <!-- Questions 1-3: Text Entry -->
                <div class="question-card">
                    <div class="question-number">Question 1</div>
                    <p><strong>What is your name?</strong> <span class="required-star">*</span></p>
                    <input type="text" class="form-control" name="q1_text" 
                           placeholder="Enter your answer" 
                           value="<?php echo isset($_POST['q1_text']) ? htmlspecialchars($_POST['q1_text']) : ''; ?>">
                </div>

                <div class="question-card">
                    <div class="question-number">Question 2</div>
                    <p><strong>What is your favorite subject?</strong> <span class="required-star">*</span></p>
                    <input type="text" class="form-control" name="q2_text" 
                           placeholder="Enter your answer"
                           value="<?php echo isset($_POST['q2_text']) ? htmlspecialchars($_POST['q2_text']) : ''; ?>">
                </div>

                <div class="question-card">
                    <div class="question-number">Question 3</div>
                    <p><strong>What are your hobbies?</strong> <span class="required-star">*</span></p>
                    <input type="text" class="form-control" name="q3_text" 
                           placeholder="Enter your answer"
                           value="<?php echo isset($_POST['q3_text']) ? htmlspecialchars($_POST['q3_text']) : ''; ?>">
                </div>

                <!-- Questions 4-7: Radio Buttons -->
                <div class="question-card">
                    <div class="question-number">Question 4</div>
                    <p><strong>What is the capital of the Philippines?</strong> <span class="required-star">*</span></p>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="q4_radio" value="Manila" 
                                   <?php echo (isset($_POST['q4_radio']) && $_POST['q4_radio'] == 'Manila') ? 'checked' : ''; ?>> 
                            Manila
                        </label>
                        <label>
                            <input type="radio" name="q4_radio" value="Cebu" 
                                   <?php echo (isset($_POST['q4_radio']) && $_POST['q4_radio'] == 'Cebu') ? 'checked' : ''; ?>> 
                            Cebu
                        </label>
                        <label>
                            <input type="radio" name="q4_radio" value="Davao" 
                                   <?php echo (isset($_POST['q4_radio']) && $_POST['q4_radio'] == 'Davao') ? 'checked' : ''; ?>> 
                            Davao
                        </label>
                    </div>
                </div>

                <div class="question-card">
                    <div class="question-number">Question 5</div>
                    <p><strong>What is 2 + 2?</strong> <span class="required-star">*</span></p>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="q5_radio" value="3" 
                                   <?php echo (isset($_POST['q5_radio']) && $_POST['q5_radio'] == '3') ? 'checked' : ''; ?>> 
                            3
                        </label>
                        <label>
                            <input type="radio" name="q5_radio" value="4" 
                                   <?php echo (isset($_POST['q5_radio']) && $_POST['q5_radio'] == '4') ? 'checked' : ''; ?>> 
                            4
                        </label>
                        <label>
                            <input type="radio" name="q5_radio" value="5" 
                                   <?php echo (isset($_POST['q5_radio']) && $_POST['q5_radio'] == '5') ? 'checked' : ''; ?>> 
                            5
                        </label>
                    </div>
                </div>

                <div class="question-card">
                    <div class="question-number">Question 6</div>
                    <p><strong>What is the color of the sky?</strong> <span class="required-star">*</span></p>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="q6_radio" value="Red" 
                                   <?php echo (isset($_POST['q6_radio']) && $_POST['q6_radio'] == 'Red') ? 'checked' : ''; ?>> 
                            Red
                        </label>
                        <label>
                            <input type="radio" name="q6_radio" value="Blue" 
                                   <?php echo (isset($_POST['q6_radio']) && $_POST['q6_radio'] == 'Blue') ? 'checked' : ''; ?>> 
                            Blue
                        </label>
                        <label>
                            <input type="radio" name="q6_radio" value="Green" 
                                   <?php echo (isset($_POST['q6_radio']) && $_POST['q6_radio'] == 'Green') ? 'checked' : ''; ?>> 
                            Green
                        </label>
                    </div>
                </div>

                <div class="question-card">
                    <div class="question-number">Question 7</div>
                    <p><strong>Which is the largest planet?</strong> <span class="required-star">*</span></p>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="q7_radio" value="Earth" 
                                   <?php echo (isset($_POST['q7_radio']) && $_POST['q7_radio'] == 'Earth') ? 'checked' : ''; ?>> 
                            Earth
                        </label>
                        <label>
                            <input type="radio" name="q7_radio" value="Mars" 
                                   <?php echo (isset($_POST['q7_radio']) && $_POST['q7_radio'] == 'Mars') ? 'checked' : ''; ?>> 
                            Mars
                        </label>
                        <label>
                            <input type="radio" name="q7_radio" value="Jupiter" 
                                   <?php echo (isset($_POST['q7_radio']) && $_POST['q7_radio'] == 'Jupiter') ? 'checked' : ''; ?>> 
                            Jupiter
                        </label>
                    </div>
                </div>

                <!-- Questions 8-10: File Uploads -->
                <div class="question-card">
                    <div class="question-number">Question 8</div>
                    <p><strong>Upload a document</strong> <span class="required-star">*</span></p>
                    <div class="file-upload">
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                        <p class="mt-2">Click to upload a file (PDF, DOC, JPG, PNG)</p>
                        <input type="file" class="form-control" name="q8_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        <small class="text-muted">Maximum file size: 5MB</small>
                    </div>
                </div>

                <div class="question-card">
                    <div class="question-number">Question 9</div>
                    <p><strong>Upload a picture</strong> <span class="required-star">*</span></p>
                    <div class="file-upload">
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                        <p class="mt-2">Click to upload an image (JPG, PNG, GIF)</p>
                        <input type="file" class="form-control" name="q9_file" accept=".jpg,.jpeg,.png,.gif">
                        <small class="text-muted">Maximum file size: 5MB</small>
                    </div>
                </div>

                <div class="question-card">
                    <div class="question-number">Question 10</div>
                    <p><strong>Upload additional document</strong> <span class="required-star">*</span></p>
                    <div class="file-upload">
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                        <p class="mt-2">Click to upload a file (PDF, DOC, JPG, PNG)</p>
                        <input type="file" class="form-control" name="q10_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        <small class="text-muted">Maximum file size: 5MB</small>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Quiz
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>