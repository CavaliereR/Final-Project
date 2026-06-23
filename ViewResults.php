<?php

include("Database.php");

$sql =
"SELECT
results.*,
users.fullname,
quizzes.quizTitle

FROM results

INNER JOIN users
ON users.userID =
results.studentID

INNER JOIN quizzes
ON quizzes.quizID =
results.quizID";

$result =
mysqli_query(
$conn,
$sql
);

?>

<!DOCTYPE html>
<html>
<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Student Results</h2>

<table class="table table-bordered">

<tr>

<th>Student</th>
<th>Quiz</th>
<th>Score</th>
<th>Date</th>

</tr>

<?php

while(
$row=mysqli_fetch_assoc($result)
)
{

?>

<tr>

<td>
<?php echo $row['fullname']; ?>
</td>

<td>
<?php echo $row['quizTitle']; ?>
</td>

<td>
<?php echo $row['score']; ?>
</td>

<td>
<?php echo $row['dateTaken']; ?>
</td>

</tr>

<?php

}

?>

</table>
<a href="TeacherDashboard.php"
class="btn btn-primary">
Dashboard
</a>

<a href="ManageQuiz.php"
class="btn btn-info">
Manage Quiz
</a>
</div>

</body>
</html>