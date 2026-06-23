<?php

session_start();

include("Database.php");

$studentID =
$_SESSION['userID'];

$sql =
"SELECT
results.*,
quizzes.quizTitle

FROM results

INNER JOIN quizzes
ON quizzes.quizID =
results.quizID

WHERE studentID='$studentID'";

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

<h2>Quiz History</h2>

<table class="table table-bordered">

<tr>

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
<a href="StudentDashboard.php"
class="btn btn-primary">
Dashboard
</a>

<a href="Leaderboard.php"
class="btn btn-warning">
Leaderboard
</a>
</div>

</body>
</html>