<?php
function dbConnect() {
    if ($sqlcon->connect_error) {
        die("Connection failed:" . $sqlcon->connect_error);
    }
    return $sqlcon;
}

session_start();

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['role']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // choose file based on role
    if($role == "student") {
        $file = "students.txt";
    } else {
        $file = "teachers.txt";
    }

    $accounts = file($file, FILE_IGNORE_NEW_LINES);

    foreach($accounts as $line)
    {
        $data = explode(",", $line);

        $fileUser = $data[0];
        $filePass = $data[1];

        if($username == $fileUser && $password == $filePass)
        {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if($role == "student") {
                header("Location: quiz.php");
            } else {
                header("Location: interface.php");
            }

            exit();
        }
    }

    echo "Invalid login.";
}
?>


<html>
    <head>
        <title>Log In</title>
    </head>

    <body>
        <form method="post">

Username:
<input type="text" name="username"><br><br>

Password:
<input type="password" name="password"><br><br>

Role:
<input type="radio" name="role" value="student"> Student

<input type="radio" name="role" value="teacher"> Teacher <br> <br>

<input type="submit" value="Login">

</form>
    </body>
</html>