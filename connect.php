<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'student_leave_management';

$con = mysqli_connect($host, $user, $password, $database);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
