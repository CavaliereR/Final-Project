<?php
session_start();
include("Database.php");

$studentID = $_SESSION['userID'];

$result = mysqli_query($conn, "
    SELECT r.*, q.quizTitle 
    FROM results r
    JOIN quizzes q ON q.quizID = r.quizID
    WHERE r.studentID='$studentID'
    ORDER BY r.dateTaken DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Quiz History</h2>
        
        <table class="table table-bordered">
            <tr>
                <th>Quiz</th>
                <th>Score</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['quizTitle']); ?></td>
                <td><?php echo $row['score']; ?></td>
                <td><?php echo $row['dateTaken']; ?></td>
                <td>
                    <a href="ViewQuizAnswers.php?id=<?php echo $row['resultID']; ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> View Answers
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <a href="StudentDashboard.php" class="btn btn-primary">Dashboard</a>
        <a href="Leaderboard.php" class="btn btn-warning">Leaderboard</a>
    </div>
</body>
</html>