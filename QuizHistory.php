<?php
session_start();
include("Database.php");

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$studentID = $_SESSION['userID'];

$result = mysqli_query($conn, "
    SELECT r.*, q.quizTitle 
    FROM results r
    JOIN quizzes q ON q.quizID = r.quizID
    WHERE r.studentID='$studentID'
    ORDER BY r.dateTaken DESC
");

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
        .history-container {
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
        .btn-primary, .btn-warning, .btn-info {
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
        .btn-warning {
            background: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529;
        }
        .btn-warning:hover {
            background: #e0a800 !important;
            border-color: #e0a800 !important;
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
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
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
        <div class="history-container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-history me-2"></i> Quiz History</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
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
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i> You haven't taken any quizzes yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="StudentDashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a href="Leaderboard.php" class="btn btn-warning">
                            <i class="fas fa-trophy"></i> Leaderboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>