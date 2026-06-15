<html>
<head>
    <title>Register Student</title>
</head>
<body>

<form method="post">

    Name:
    <input type="text" name="txtName"><br><br>

    Age:
    <input type="text
    " name="txtpass"><br><br>

 
    <input type="submit" name="btnSave" value="Save">

<input type="radio" name="txtrole" value="student"> Student

<input type="radio" name="txtrole" value="teacher"> Teacher <br> <br>

</form>

<?php

$connection = mysqli_connect(
    "localhost",
    "root",
    "",
    "qez"
);

if(isset($_POST["btnSave"])&&isset($_POST["txtrole"]) && $_POST["txtrole"] == "student")
{
    $name = $_POST["txtName"];
    $pass = $_POST["txtpass"];


    $query = "
    INSERT INTO student
    (name, password)
    VALUES
    ('$name', '$pass')
    ";

    if(mysqli_query($connection, $query))
    {
        echo "Record Saved!";
           header("Location: quiz.php");
    }
    else
    {
        echo "Error Saving Record";
    }
}
if(isset($_POST["btnSave"])&&isset($_POST["txtrole"]) && $_POST["txtrole"] == "teacher"){
    $name = $_POST["txtName"];
    $pass = $_POST["txtpass"];


    $query = "
    INSERT INTO teacher
    (name, password)
    VALUES
    ('$name', '$pass')
    ";

    if(mysqli_query($connection, $query))
    {
        echo "Record Saved!";
         header("Location: interface.php");
    }
    else
    {
        echo "Error Saving Record";
    }
}

?>
</body>
</html>