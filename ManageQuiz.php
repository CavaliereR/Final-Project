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
</head>
<body>

<div class="container mt-5">
    <h2>Manage Quiz</h2>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Time</th>
            <th>Questions</th>
            <th>Actions</th>
        </tr>

        <?php
        while($row=mysqli_fetch_assoc($result)) {
            // Count questions
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
                        <span class="badge bg-warning"><?php echo $fileQ; ?> file uploads</span>
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
    </table>
    
    <a href="TeacherDashboard.php" class="btn btn-primary">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="CreateQuiz.php" class="btn btn-success">
        <i class="fas fa-plus"></i> Create Quiz
    </a>
    
</div>

</body>
</html>