<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['usertype'] != 'Teacher') {
    header("Location: index.php?error=Unauthorized Access");
    exit();
}

// Fetch pending leave applications
$leave_query = "SELECT leave_applications.*, users.name, users.email 
                FROM leave_applications 
                JOIN users ON leave_applications.student_id = users.id
                WHERE leave_applications.status = 'Pending' 
                ORDER BY leave_applications.submission_date DESC";
$leave_result = mysqli_query($con, $leave_query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decision'])) {
    $application_id = $_POST['application_id'];
    $decision = $_POST['decision'];
    $comment = $_POST['comment'];
    $teacher_id = $_SESSION['user_data']['id'];

    // Update leave status
    $update_query = "UPDATE leave_applications SET status = '$decision' WHERE id = '$application_id'";
    mysqli_query($con, $update_query);

    // Insert faculty comment
    $comment_query = "INSERT INTO faculty_comments (application_id, teacher_id, comment, decision) 
                      VALUES ('$application_id', '$teacher_id', '$comment', '$decision')";
    mysqli_query($con, $comment_query);

    header("Location: teacher_home.php?success=Leave status updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Teacher Dashboard - Leave Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 20px;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #007bff;
            font-weight: bold;
            text-align: center;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn {
            border-radius: 8px;
            padding: 10px 15px;
            font-weight: bold;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-logout {
            background-color: #ff4b5c;
            color: white;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .btn-logout:hover {
            background-color: #d43f53;
        }
        .no-applications {
            text-align: center;
            font-size: 18px;
            color: #ff3b3b;
            font-weight: bold;
        }
        .navbar {
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .navbar h3 {
            margin: 0;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Navbar with Logout Button -->
    <div class="navbar">
        <h3>Teacher Dashboard</h3>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>

    <div class="container mt-4">
        <h2>Pending Leave Applications</h2>
        <?php if (mysqli_num_rows($leave_result) > 0) { ?>
            <table class="table table-bordered table-striped mt-4">
                <thead class="table-primary">
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Reason</th>
                        <th>Document</th>
                        <th>Submission Date</th>
                        <th>Decision</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($leave_result)) { ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['reason']; ?></td>
                            <td>
                                <?php if (!empty($row['document'])) { ?>
                                    <a href="download.php?file=<?php echo urlencode($row['document']); ?>" class="btn btn-info btn-sm">View Document</a>
                                <?php } else { ?>
                                    <span class="text-danger">No Document</span>
                                <?php } ?>
                            </td>
                            <td><?php echo $row['submission_date']; ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                                    <textarea name="comment" placeholder="Add comments" required class="form-control mb-2"></textarea>
                                    <button type="submit" name="decision" value="Approved" class="btn btn-success btn-sm w-100 mb-2">Approve</button>
                                    <button type="submit" name="decision" value="Rejected" class="btn btn-danger btn-sm w-100">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="no-applications">No pending leave applications.</p>
        <?php } ?>
    </div>

</body>
</html>
