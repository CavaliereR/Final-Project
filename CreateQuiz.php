<?php
session_start();
include("Database.php");

if(isset($_POST['save']))
{
    $title = $_POST['title'];
    $description = $_POST['description'];
    $timeLimit = $_POST['timeLimit'];

    $sql = "INSERT INTO quizzes
    (quizTitle,description,timeLimit)
    VALUES
    ('$title','$description','$timeLimit')";

    mysqli_query($conn,$sql);

    echo "<div class='alert alert-success'>Quiz Created</div>";
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