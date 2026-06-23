<?php

include("Database.php");

$sql =
"SELECT
users.fullname,
SUM(results.score) totalScore

FROM results

INNER JOIN users
ON users.userID =
results.studentID

GROUP BY users.fullname

ORDER BY totalScore DESC";

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

<h2>Leaderboard</h2>

<table class="table table-bordered">

<tr>

<th>Rank</th>
<th>Student</th>
<th>Total Score</th>

</tr>

<?php

$rank = 1;

while(
$row=mysqli_fetch_assoc($result)
)
{

?>

<tr>

<td>
<?php echo $rank; ?>
</td>

<td>
<?php echo $row['fullname']; ?>
</td>

<td>
<?php echo $row['totalScore']; ?>
</td>

</tr>

<?php

$rank++;

}

?>

</table>
<a href="StudentDashboard.php"
class="btn btn-primary">
Dashboard
</a>

<a href="QuizHistory.php"
class="btn btn-info">
Quiz History
</a>
</div>

</body>
</html>