<?php
$host = "localhost";
$user = "u150718207_user";
$pass = "Vh:42s4L~sv";
$dbname = "u150718207_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>