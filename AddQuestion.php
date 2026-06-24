<?php
session_start();
include("Database.php");

// Check if teacher is logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: index.php");
    exit();
}

$quizID = $_GET['id'];
$message = '';
$error = '';

// Handle question deletion
if (isset($_GET['delete'])) {
    $questionID = $_GET['delete'];
    
    // Check if this question has submissions (file uploads)
    $checkSubmissions = mysqli_query($conn, "SELECT * FROM submissions WHERE questionID='$questionID'");
    if (mysqli_num_rows($checkSubmissions) > 0) {
        $error = "Cannot delete this question because students have already submitted files for it.";
    } else {
        // Delete the question
        $deleteQuery = "DELETE FROM questions WHERE questionID='$questionID' AND quizID='$quizID'";
        if (mysqli_query($conn, $deleteQuery)) {
            $message = "Question deleted successfully!";
        } else {
            $error = "Error deleting question: " . mysqli_error($conn);
        }
    }
}

if(isset($_POST['save']))
{
    $question = trim($_POST['question']);
    $question_type = $_POST['question_type'];
    
    // Validate question text
    if (empty($question)) {
        $error = "Question text is required.";
    } elseif ($question_type == 'mcq') {
        $a = trim($_POST['a']);
        $b = trim($_POST['b']);
        $c = trim($_POST['c']);
        $d = trim($_POST['d']);
        $answer = trim($_POST['answer']);
        
        // Validate MCQ fields
        if (empty($a) || empty($b) || empty($c) || empty($d)) {
            $error = "All choices (A, B, C, D) are required.";
        } elseif (empty($answer)) {
            $error = "Correct answer is required. Please enter the correct choice.";
        } else {
            // Check if answer matches one of the choices
            $validAnswers = array($a, $b, $c, $d);
            if (!in_array($answer, $validAnswers)) {
                $error = "Correct answer must match one of the provided choices.";
            } else {
                $sql = "INSERT INTO questions (quizID, questionText, choiceA, choiceB, choiceC, choiceD, answer, question_type)
                        VALUES ('$quizID', '$question', '$a', '$b', '$c', '$d', '$answer', 'mcq')";
                
                if (mysqli_query($conn, $sql)) {
                    $message = "Question Added Successfully!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    } else {
        // File upload question - no choices needed
        $sql = "INSERT INTO questions (quizID, questionText, choiceA, choiceB, choiceC, choiceD, answer, question_type)
                VALUES ('$quizID', '$question', '', '', '', '', '', 'file')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "Question Added Successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Get quiz title for display
$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM quizzes WHERE quizID='$quizID'"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .question-type-selector {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .question-type-selector label {
            cursor: pointer;
            padding: 10px 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .question-type-selector input[type="radio"]:checked + label {
            border-color: #8B0000;
            background-color: #f8f9fa;
        }
        .required-field::after {
            content: " *";
            color: red;
            font-weight: bold;
        }
        .question-actions {
            white-space: nowrap;
        }
        .delete-btn {
            color: #dc3545;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .delete-btn:hover {
            color: #a71d2a;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Add Question to: <?php echo htmlspecialchars($quiz['quizTitle']); ?></h4>
        </div>
        <div class="card-body">
            <?php if($message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST">

                <div class="mb-3">
                    <label class="form-label fw-bold">Question Type</label>
                    <div class="question-type-selector">
                        <div>
                            <input type="radio" name="question_type" value="mcq" id="mcq" checked>
                            <label for="mcq">
                                <i class="fas fa-list-ul"></i> Multiple Choice
                            </label>
                        </div>
                        <div>
                            <input type="radio" name="question_type" value="file" id="file">
                            <label for="file">
                                <i class="fas fa-upload"></i> File Upload
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold required-field">Question</label>
                    <textarea name="question" class="form-control" rows="3" placeholder="Enter your question" required></textarea>
                </div>

                <!-- MCQ Fields -->
                <div id="mcqFields">
                    <div class="mb-2">
                        <label class="form-label required-field">Choice A</label>
                        <input type="text" name="a" placeholder="Choice A" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label required-field">Choice B</label>
                        <input type="text" name="b" placeholder="Choice B" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label required-field">Choice C</label>
                        <input type="text" name="c" placeholder="Choice C" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label required-field">Choice D</label>
                        <input type="text" name="d" placeholder="Choice D" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold required-field">Correct Answer</label>
                        <input type="text" name="answer" placeholder="Enter the correct answer (must match one of the choices above)" class="form-control" required>
                        <small class="text-muted">The correct answer must exactly match one of the choices above.</small>
                    </div>
                </div>

                <!-- File Upload Info -->
                <div id="fileFields" style="display:none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>File Upload Question:</strong> Students will be required to upload a file for this question.
                        <br>
                        <small>Accepted file types: JPG, PNG, GIF, PDF, DOC, TXT (Max 5MB)</small>
                    </div>
                </div>

                <input type="submit" name="save" value="Add Question" class="btn btn-success">
                <a href="ManageQuiz.php" class="btn btn-secondary">Back to Manage Quiz</a>
                <a href="TeacherDashboard.php" class="btn btn-primary">Dashboard</a>

            </form>
        </div>
    </div>

    <!-- Show existing questions with delete option -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Existing Questions</h5>
            <span class="badge bg-light text-dark"><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM questions WHERE quizID='$quizID'")); ?> questions</span>
        </div>
        <div class="card-body">
            <?php
            $questions = mysqli_query($conn, "SELECT * FROM questions WHERE quizID='$quizID' ORDER BY questionID ASC");
            if (mysqli_num_rows($questions) > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered table-hover'>";
                echo "<thead class='table-light'>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Answer</th>
                            <th>Actions</th>
                        </tr>
                      </thead>";
                echo "<tbody>";
                $num = 1;
                while ($q = mysqli_fetch_assoc($questions)) {
                    $type = ($q['question_type'] == 'file') ? 
                            '<span class="badge bg-warning"><i class="fas fa-upload"></i> File Upload</span>' : 
                            '<span class="badge bg-primary"><i class="fas fa-list-ul"></i> Multiple Choice</span>';
                    
                    $answer = ($q['question_type'] == 'file') ? 
                              '<span class="text-muted"><i>File upload (no specific answer)</i></span>' : 
                              '<span class="badge bg-success">' . htmlspecialchars($q['answer']) . '</span>';
                    
                    $questionText = htmlspecialchars($q['questionText']);
                    if (strlen($questionText) > 50) {
                        $questionText = substr($questionText, 0, 50) . '...';
                    }
                    
                    echo "<tr>
                            <td>{$num}</td>
                            <td>{$questionText}</td>
                            <td>{$type}</td>
                            <td>{$answer}</td>
                            <td class='question-actions'>
                                <a href='AddQuestion.php?id={$quizID}&delete={$q['questionID']}' 
                                class='btn btn-danger btn-sm' 
                                onclick='return confirm(\"Delete this question?\");'>
                                    <i class='fas fa-trash'></i> Delete
                                </a>
                            </td>
                          </tr>";
                    $num++;
                }
                echo "</tbody></table>";
                echo "</div>";
            } else {
                echo "<div class='text-center py-4'>
                        <i class='fas fa-question-circle fa-3x text-muted mb-3'></i>
                        <p class='text-muted'>No questions added yet.</p>
                        <p class='text-muted small'>Add your first question using the form above.</p>
                      </div>";
            }
            ?>
        </div>
    </div>
</div>

<script>
    // Toggle between MCQ and File Upload fields
    document.querySelectorAll('input[name="question_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'mcq') {
                document.getElementById('mcqFields').style.display = 'block';
                document.getElementById('fileFields').style.display = 'none';
                // Make MCQ fields required
                document.querySelectorAll('#mcqFields input').forEach(function(input) {
                    if (input.name !== 'answer') {
                        input.required = true;
                    }
                });
                document.querySelector('input[name="answer"]').required = true;
            } else {
                document.getElementById('mcqFields').style.display = 'none';
                document.getElementById('fileFields').style.display = 'block';
                // Make MCQ fields not required
                document.querySelectorAll('#mcqFields input').forEach(function(input) {
                    input.required = false;
                });
            }
        });
    });
</script>

<!-- Bootstrap JS for alert dismissal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>