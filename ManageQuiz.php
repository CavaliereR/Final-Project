<?php
include("Database.php");

$result = mysqli_query($conn, "SELECT * FROM quizzes");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .manage-container {
            max-width: 1100px;
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
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 3px;
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
            margin-right: 6px;
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
            margin-top: 20px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        .badge-warning {
            background: #ffc107;
            color: #212529;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="manage-container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-tasks me-2"></i> Manage Quiz</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Time</th>
                                    <th>Questions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_assoc($result)) {
                                    $countQuery = mysqli_query($conn, "SELECT COUNT(*) as total, 
                                                                    SUM(CASE WHEN question_type='file' THEN 1 ELSE 0 END) as file_count 
                                                                    FROM questions WHERE quizID='" . $row['quizID'] . "'");
                                    $countData = mysqli_fetch_assoc($countQuery);
                                    $totalQ = $countData['total'] ?? 0;
                                    $fileQ = $countData['file_count'] ?? 0;
                                ?>
                                <tr>
                                    <td><?php echo $row['quizID']; ?></td>
                                    <td><?php echo htmlspecialchars($row['quizTitle']); ?></td>
                                    <td><?php echo $row['timeLimit']; ?> mins</td>
                                    <td>
                                        <?php echo $totalQ; ?> total 
                                        <?php if ($fileQ > 0): ?>
                                            <span class="badge bg-warning text-dark"><?php echo $fileQ; ?> file uploads</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="AddQuestion.php?id=<?php echo $row['quizID']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> Questions
                                        </a>
                                        <a href="EditQuiz.php?id=<?php echo $row['quizID']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="DeleteQuiz.php?id=<?php echo $row['quizID']; ?>" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to delete this quiz?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="TeacherDashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a href="CreateQuiz.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>