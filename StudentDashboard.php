<?php

session_start();
include("Database.php");

$result =
mysqli_query(
$conn,
"SELECT * FROM quizzes"
);

?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Available Quizzes</h2>

<table class="table table-bordered">

<tr>
<th>Quiz ID</th>
<th>Title</th>
<th>Description</th>
<th>Time Limit</th>
<th>Action</th>
</tr>

<?php

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td><?php echo $row['quizID']; ?></td>

<td><?php echo $row['quizTitle']; ?></td>

<td><?php echo $row['description']; ?></td>

<td><?php echo $row['timeLimit']; ?> mins</td>

<td>

<a href="TakeQuiz.php?id=<?php echo $row['quizID']; ?>"
class="btn btn-success">
Take Quiz
</a>

</td>

</tr>

<?php

}

?>

</table>

<a href="QuizHistory.php"
class="btn btn-primary">
Quiz History
</a>

<a href="Leaderboard.php"
class="btn btn-warning">
Leaderboard
</a>

<a href="Logout.php"
class="btn btn-danger">
Logout
</a>


</div>

</body>
</html>