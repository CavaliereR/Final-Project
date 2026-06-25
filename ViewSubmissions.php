<?php
session_start();
include("Database.php");

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: index.php");
    exit();
}

$quizID = isset($_GET['id']) ? $_GET['id'] : 0;


$submissions = mysqli_query($conn, "
    SELECT s.*, u.fullname, q.questionText 
    FROM submissions s
    JOIN users u ON s.studentID = u.userID
    JOIN questions q ON s.questionID = q.questionID
    WHERE s.quizID = '$quizID'
    ORDER BY s.submitted_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .submissions-container {
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
        .btn-primary {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
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
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-success:hover {
            background: #218838 !important;
            border-color: #218838 !important;
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
        .form-control-sm {
            display: inline-block;
            width: 70px;
        }
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="submissions-container">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-upload me-2"></i> Student File Submissions</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Question</th>
                                    <th>File</th>
                                    <th>Download</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($submissions)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['fullname - ViewSubmissions.php:139']); ?></td>
                                    <td><?php echo htmlspecialchars($row['questionText - ViewSubmissions.php:140']); ?></td>
                                    <td><?php echo basename($row['filePath - ViewSubmissions.php:141']); ?></td>
                                    <td>
                                        <a href="<?php echo $row['filePath']; ?> - ViewSubmissions.php:143" class="btn btn-primary btn-sm" download>
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="score_submission.php" style="display:inline;">
                                            <input type="hidden - ViewSubmissions.php:149" name="submissionID" value="<?php echo $row['submissionID']; ?>">
                                            <input type="number" name="score" min="0" max="10" class="form-control form-control-sm" style="display:inline-block;width:70px;">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-save"></i> Save
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="TeacherDashboard.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>