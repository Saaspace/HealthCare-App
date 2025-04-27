<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['usertype'] != 2) {
    header("Location: teacher_home.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_data']['id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = mysqli_real_escape_string($con, $_POST['reason']);

    if (empty($start_date) || empty($end_date) || empty($reason)) {
        $error = "All fields are required!";
    } elseif ($start_date > $end_date) {
        $error = "Start date cannot be after end date!";
    } else {
        $query = "INSERT INTO leave_applications (student_id, start_date, end_date, reason) VALUES ('$student_id', '$start_date', '$end_date', '$reason')";
        if (mysqli_query($con, $query)) {
            $success = "Leave application submitted successfully!";
        } else {
            $error = "Error submitting application: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Leave</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Apply for Leave</h4>
            </div>
            <div class="card-body">
                <?php if ($error) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                <?php if ($success) { echo "<div class='alert alert-success'>$success</div>"; } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Submit Application</button>
                    <a href="view_leave_status.php" class="btn btn-secondary">View Leave Status</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
