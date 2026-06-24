<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['save']))
{
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $timeLimit = (int)$_POST['timeLimit'];
    
    // Validation
    if(empty($title)) {
        $error = "Quiz title is required.";
    } elseif(empty($description)) {
        $error = "Quiz description is required.";
    } elseif($timeLimit < 1) {
        $error = "Time limit must be at least 1 minute.";
    } else {
        $sql = "INSERT INTO quizzes (quizTitle, description, timeLimit) 
                VALUES ('$title','$description','$timeLimit')";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Quiz Created Successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Quiz</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2>Create Quiz</h2>

<form method="POST">

<label>Quiz Title</label>
<input type="text"
name="title"
class="form-control mb-3">

<label>Description</label>
<textarea
name="description"
class="form-control mb-3">
</textarea>

<label>Time Limit (Minutes)</label>
<input type="number"
name="timeLimit"
class="form-control mb-3">

<input
type="submit"
name="save"
value="Create Quiz"
class="btn btn-success">

<a href="TeacherDashboard.php"
class="btn btn-secondary">
Back
</a>
<br><br>

<a href="TeacherDashboard.php"
class="btn btn-secondary">
Dashboard
</a>

<a href="ManageQuiz.php"
class="btn btn-info">
Manage Quiz
</a>

</form>

</div>

</body>
</html>