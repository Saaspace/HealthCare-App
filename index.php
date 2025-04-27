<?php
session_start();
include 'connect.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_data'] = $user;
            if ($user['usertype'] == 'Student') {
                header('Location: student_home.php');
            } else {
                header('Location: teacher_home.php');
            }
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Student Leave Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Colorful Background */
        body {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Card Styling */
        .card {
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            padding: 25px;
            width: 400px;
        }

        /* Form Input Fields */
        .form-control {
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
        }

        /* Buttons */
        .btn-primary {
            background-color: #ff6f61;
            border: none;
            font-size: 18px;
            border-radius: 10px;
            padding: 12px;
        }

        .btn-primary:hover {
            background-color: #ff3b3b;
        }

        .text-primary {
            color: #ff6f61 !important;
        }

        .error-msg {
            color: red;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2 class="text-center text-primary fw-bold">Login</h2>
        <?php if (isset($error)) { echo "<p class='error-msg'>$error</p>"; } ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3">Don't have an account? <a href="register.php" class="text-success fw-bold">Register</a></p>
    </div>
</body>
</html>
