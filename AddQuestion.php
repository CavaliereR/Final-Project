<?php
session_start();
include("Database.php");

// Check if teacher is logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: Login.php");
    exit();
}

$quizID = $_GET['id'];

if(isset($_POST['save']))
{
    $question = $_POST['question'];
    $question_type = $_POST['question_type'];
    
    if ($question_type == 'mcq') {
        $a = $_POST['a'];
        $b = $_POST['b'];
        $c = $_POST['c'];
        $d = $_POST['d'];
        $answer = $_POST['answer'];
        
        $sql = "INSERT INTO questions (quizID, questionText, choiceA, choiceB, choiceC, choiceD, answer, question_type)
                VALUES ('$quizID', '$question', '$a', '$b', '$c', '$d', '$answer', 'mcq')";
    } else {
        // File upload question - no choices needed
        $sql = "INSERT INTO questions (quizID, questionText, choiceA, choiceB, choiceC, choiceD, answer, question_type)
                VALUES ('$quizID', '$question', '', '', '', '', '', 'file')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success'>Question Added Successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
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
        .mcq-fields {
            display: block;
        }
        .file-fields {
            display: none;
        }
        .file-fields.active {
            display: block;
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
                    <label class="form-label fw-bold">Question</label>
                    <textarea name="question" class="form-control" rows="3" placeholder="Enter your question" required></textarea>
                </div>

                <!-- MCQ Fields -->
                <div id="mcqFields">
                    <div class="mb-2">
                        <label class="form-label">Choice A</label>
                        <input type="text" name="a" placeholder="Choice A" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Choice B</label>
                        <input type="text" name="b" placeholder="Choice B" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Choice C</label>
                        <input type="text" name="c" placeholder="Choice C" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Choice D</label>
                        <input type="text" name="d" placeholder="Choice D" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correct Answer</label>
                        <input type="text" name="answer" placeholder="Enter the correct answer" class="form-control">
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

    <!-- Show existing questions -->
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5>Existing Questions</h5>
        </div>
        <div class="card-body">
            <?php
            $questions = mysqli_query($conn, "SELECT * FROM questions WHERE quizID='$quizID'");
            if (mysqli_num_rows($questions) > 0) {
                echo "<table class='table table-bordered'>";
                echo "<tr><th>#</th><th>Question</th><th>Type</th></tr>";
                $num = 1;
                while ($q = mysqli_fetch_assoc($questions)) {
                    $type = ($q['question_type'] == 'file') ? '<span class="badge bg-warning">File Upload</span>' : '<span class="badge bg-primary">Multiple Choice</span>';
                    echo "<tr>
                            <td>{$num}</td>
                            <td>" . htmlspecialchars($q['questionText']) . "</td>
                            <td>{$type}</td>
                          </tr>";
                    $num++;
                }
                echo "</table>";
            } else {
                echo "<p class='text-muted'>No questions added yet.</p>";
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
                // Make fields required
                document.querySelectorAll('#mcqFields input').forEach(function(input) {
                    input.required = true;
                });
            } else {
                document.getElementById('mcqFields').style.display = 'none';
                document.getElementById('fileFields').style.display = 'block';
                // Make fields not required
                document.querySelectorAll('#mcqFields input').forEach(function(input) {
                    input.required = false;
                });
            }
        });
    });
</script>

</body>
</html>