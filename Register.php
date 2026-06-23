<?php
include("Database.php");

if(isset($_POST['register']))
{
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $password = password_hash($password,PASSWORD_DEFAULT);

    $sql = "INSERT INTO users
            (fullname,email,password,role)
            VALUES
            ('$fullname','$email','$password','$role')";

    mysqli_query($conn,$sql);

    echo "Registration Successful";
}
?>

<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<form method="POST">

<input type="text"
name="fullname"
placeholder="Full Name"
class="form-control mb-2">

<input type="email"
name="email"
placeholder="Email"
class="form-control mb-2">

<input type="password"
name="password"
placeholder="Password"
class="form-control mb-2">

<select name="role" class="form-control mb-2">
<option>Student</option>
<option>Teacher</option>
</select>

<input type="submit"
name="register"
value="Register"
class="btn btn-success">

<a href="Login.php"
class="btn btn-secondary">
Back to Login
</a>
</form>

</div>

</body>
</html>