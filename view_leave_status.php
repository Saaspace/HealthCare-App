<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['usertype'] != 2) {
    header("Location: teacher_home.php");
    exit();
}

$student_id = $_SESSION['user_data']['id'];
$leave_data = [];
$is_leave_found = false;

// Fetch leave applications for the logged-in student
$leave_query = mysqli_query($con, "SELECT * FROM leave_applications WHERE student_id = '$student_id' ORDER BY created_at DESC");

if (mysqli_num_rows($leave_query) > 0) {
    $is_leave_found = true;
    while ($row = mysqli_fetch_assoc($leave_query)) {
        array_push($leave_data, $row);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Leave Status - Student Leave Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <!-- Navigation -->
        <div class="mb-3">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <!-- Leave Status Table -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Your Leave Applications</h4>
            </div>
            <div class="card-body">
                <?php if ($is_leave_found) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Application ID</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leave_data as $leave) { ?>
                                <tr>
                                    <td><?php echo $leave['id']; ?></td>
                                    <td><?php echo $leave['start_date']; ?></td>
                                    <td><?php echo $leave['end_date']; ?></td>
                                    <td><?php echo $leave['reason']; ?></td>
                                    <td>
                                        <?php
                                            if ($leave['status'] == 'Pending') {
                                                echo '<span class="badge bg-warning">Pending</span>';
                                            } elseif ($leave['status'] == 'Approved') {
                                                echo '<span class="badge bg-success">Approved</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">Rejected</span>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo $leave['comments'] ? $leave['comments'] : 'N/A'; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <h4 class="text-danger">No Leave Applications Found!</h4>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
