<?php
session_start();
include 'connect.php'; // Include database connection

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password'])) { // Use password_hash() in the database
            $_SESSION['user_data'] = $user;
            if($user['usertype'] == 1) {
                header("Location: teacher_home.php");
            } else {
                header("Location: student_home.php");
            }
            exit();
        } else {
            header("Location: index.php?error=Incorrect Password");
            exit();
        }
    } else {
        header("Location: index.php?error=User not found");
        exit();
    }
}
?>
