<?php
function dbConnect() {
    $sqlcon = new mysqli("localhost", 'root', '', 'php_finalproject');
    if ($sqlcon->connect_error) {
        die("Connection failed:" . $sqlcon->connect_error);
    }
    return $sqlcon;
}

function addQuiz($student_name, $Q1, $Q2, $Q3, $Q4, $Q5, $Q6, $Q7, $Q8, $Q9, $Q10, $Score) {
    $sqlcon = dbConnect();
    $stmt = $sqlcon->prepare('INSERT INTO quizzes (StudentName,Q1,Q2,Q3,Q4,Q5,Q6,Q7,Q8,Q9,Q10,Score) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
    if (! $stmt) {
        return 'Prepare failed: ' . $sqlcon->error;
    }

    $stmt->bind_param('sssssssssssi', $student_name, $Q1, $Q2, $Q3, $Q4, $Q5, $Q6, $Q7, $Q8, $Q9, $Q10, $Score);
    if ($stmt->execute()) {
        $stmt->close();
        $sqlcon->close();
        return true;
    }

    $error = 'Insert failed: ' . $stmt->error;
    $stmt->close();
    $sqlcon->close()
    return $error;

}

?>
