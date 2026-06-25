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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .dashboard-container {
            max-width: 1000px;
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
        .btn-primary, .btn-success, .btn-warning, .btn-danger {
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
        .btn-success {
            background: #28a745 !important;
            border-color: #28a745 !important;
        }
        .btn-success:hover {
            background: #218838 !important;
            border-color: #218838 !important;
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
        .btn-danger {
            background: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        .btn-danger:hover {
            background: #c82333 !important;
            border-color: #c82333 !important;
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
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn-sm {
            padding: 8px 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-container">
            <div class="card">
                <div class="card-header">
                    <h2>📚 Available Quizzes</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Quiz ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Time Limit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        <a href="TakeQuiz.php?id=<?php echo $row['quizID']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-play"></i> Take Quiz
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="action-buttons mt-3">
                        <a href="QuizHistory.php" class="btn btn-primary">
                            <i class="fas fa-history"></i> Quiz History
                        </a>
                        <a href="Leaderboard.php" class="btn btn-warning">
                            <i class="fas fa-trophy"></i> Leaderboard
                        </a>
                        <a href="Logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>