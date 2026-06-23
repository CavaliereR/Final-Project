<?php

include("Database.php");

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM quizzes
WHERE quizID='$id'"
);

header("Location: ManageQuiz.php");

?>