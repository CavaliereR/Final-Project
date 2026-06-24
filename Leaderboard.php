<?php
include("Database.php");

$sql = "SELECT
    users.fullname,
    COUNT(results.resultID) as quizzes_taken,
    SUM(results.score) totalScore,
    AVG(results.score) averageScore
FROM results
INNER JOIN users ON users.userID = results.studentID
GROUP BY users.fullname
ORDER BY totalScore DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>🏆 Leaderboard</h2>
        <p class="text-muted">Ranked by Total Score</p>
        
        <table class="table table-bordered">
            <tr>
                <th>Rank</th>
                <th>Student</th>
                <th>Quizzes Taken</th>
                <th>Total Score</th>
                <th>Average Score</th>
            </tr>
            <?php
            $rank = 1;
            while($row = mysqli_fetch_assoc($result)):
            ?>
            <tr>
                <td><?php echo $rank; ?></td>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo $row['quizzes_taken']; ?></td>
                <td><?php echo $row['totalScore']; ?></td>
                <td><?php echo number_format($row['averageScore'], 2); ?></td>
            </tr>
            <?php $rank++; endwhile; ?>
        </table>
        <a href="StudentDashboard.php" class="btn btn-primary">Dashboard</a>
        <a href="QuizHistory.php" class="btn btn-info">Quiz History</a>
    </div>
</body>
</html>