<?php
function dbConnect() {
    $sqlcon = new mysqli("localhost", 'root', '', 'php_finalproject');
    if ($sqlcon->connect_error) {
        die("Connection failed:" . $sqlcon->connect_error);
    }
    return $sqlcon;
}


?>