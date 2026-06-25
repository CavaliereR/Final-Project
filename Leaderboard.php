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

// Check if query failed
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .leaderboard-container {
            max-width: 900px;
            margin: 50px auto;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            background: #8B0000 !important;
            color: white !important;
            padding: 20px;
            border: none;
        }
        .card-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .card-body {
            padding: 30px;
        }
        .btn-primary, .btn-info {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-primary {
            background: #8B0000 !important;
            border-color: #8B0000 !important;
        }
        .btn-primary:hover {
            background: #6d0000 !important;
            border-color: #6d0000 !important;
        }
        .btn-info {
            background: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }
        .btn-info:hover {
            background: #138496 !important;
            border-color: #138496 !important;
        }
        .btn i {
            margin-right: 8px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: #8B0000 !important;
            color: white !important;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .rank-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 14px;
            background: #f8f9fa;
            color: #333;
        }
        .rank-1 { background: #ffd700; color: #333; }
        .rank-2 { background: #c0c0c0; color: #333; }
        .rank-3 { background: #cd7f32; color: white; }
        .trophy-icon { color: #ffd700; }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
        .no-results {
            text-align: center;
            padding: 40px 0;
            color: #6c757d;
        }
        .no-results i {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="leaderboard-container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-trophy me-2"></i> Leaderboard</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted">Ranked by Total Score</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Student</th>
                                    <th>Quizzes Taken</th>
                                    <th>Total Score</th>
                                    <th>Average Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $rank = 1;
                                    while($row = mysqli_fetch_assoc($result)):
                                        $rankClass = '';
                                        if ($rank == 1) $rankClass = 'rank-1';
                                        elseif ($rank == 2) $rankClass = 'rank-2';
                                        elseif ($rank == 3) $rankClass = 'rank-3';
                                ?>
                                <tr>
                                    <td>
                                        <span class="rank-badge <?php echo $rankClass; ?>">
                                            <?php echo $rank; ?>
                                        </span>
                                        <?php if ($rank <= 3): ?>
                                            <i class="fas fa-star trophy-icon"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                                    <td><?php echo $row['quizzes_taken']; ?></td>
                                    <td><?php echo $row['totalScore']; ?></td>
                                    <td><?php echo number_format($row['averageScore'], 2); ?></td>
                                </tr>
                                <?php 
                                    $rank++; 
                                    endwhile; 
                                } else {
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-2"></i> No results found. Start taking quizzes to appear on the leaderboard!
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="StudentDashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a href="QuizHistory.php" class="btn btn-info">
                            <i class="fas fa-history"></i> Quiz History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>