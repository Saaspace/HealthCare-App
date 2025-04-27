<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $usertype = $_POST['usertype']; // Student or Teacher

    $query = "INSERT INTO users (name, email, password, usertype) VALUES ('$name', '$email', '$password', '$usertype')";
    
    if (mysqli_query($con, $query)) {
        echo "<script>alert('Registration successful! Redirecting to homepage...'); window.location='index.php';</script>";
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
        }
    </style>
</head>
<body>

<div class="register-container">
    <h3 class="text-center text-primary">Register</h3>
    
    <?php if (isset($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">User Type</label>
            <select name="usertype" class="form-select">
                <option value="Student">Student</option>
                <option value="Teacher">Teacher</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <p class="text-center mt-3">
        Already have an account? <a href="index.php" class="text-decoration-none text-primary">Go to Home</a>
    </p>
</div>

</body>
</html>
