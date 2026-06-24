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
    <style>
body{
    background:#f8f9fa;
}

.card{
    border:none;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.card-header{
    background:#dc3545 !important;
    color:white !important;
}

.btn-primary,
.btn-success,
.bg-primary{
    background:#dc3545 !important;
    border-color:#dc3545 !important;
}

.btn-primary:hover,
.btn-success:hover{
    background:#bb2d3b !important;
    border-color:#bb2d3b !important;
}

.form-control:focus{
    border-color:#dc3545;
    box-shadow:0 0 0 .25rem rgba(220,53,69,.15);
}

.table thead th{
    background:#dc3545 !important;
    color:white !important;
}

.table tbody tr:hover{
    background:#fff5f5;
}

.alert{
    border-radius:12px;
}

.btn{
    border-radius:10px;
}

.badge.bg-primary{
    background:#dc3545 !important;
}
</style>
    <title>View Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container py-5">
        <h2>Student File Submissions</h2>
        
        <table class="table table-bordered">
            <tr>
                <th>Student</th>
                <th>Question</th>
                <th>File</th>
                <th>Download</th>
                <th>Score</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($submissions)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['questionText']); ?></td>
                <td><?php echo basename($row['filePath']); ?></td>
                <td>
                    <a href="<?php echo $row['filePath']; ?>" class="btn btn-primary btn-sm" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                </td>
                <td>
                    <form method="POST" action="score_submission.php" style="display:inline;">
                        <input type="hidden" name="submissionID" value="<?php echo $row['submissionID']; ?>">
                        <input type="number" name="score" min="0" max="10" class="form-control" style="width:70px;display:inline;">
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <a href="TeacherDashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>