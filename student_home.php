<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['usertype'] != 'Student') {
    header("Location: index.php?error=Unauthorized Access");
    exit();
}

$student_id = $_SESSION['user_data']['id'];

// Fetch last absent date
$last_absent_query = "SELECT deadline FROM leave_applications 
                      WHERE student_id = '$student_id' 
                      AND status = 'Approved' 
                      ORDER BY deadline DESC LIMIT 1";
$last_absent_result = mysqli_query($con, $last_absent_query);
$last_absent_date = mysqli_fetch_assoc($last_absent_result)['deadline'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reason = $_POST['reason'];
    $document = $_FILES['document']['name'];
    $target = "uploads/" . basename($document);
    move_uploaded_file($_FILES['document']['tmp_name'], $target);

    $submission_date = date('Y-m-d'); // Today's date
    $status = "Pending"; // Default status

    if ($last_absent_date) {
        $last_absent_timestamp = strtotime($last_absent_date);
        $submission_timestamp = strtotime($submission_date);
        $date_diff = ($submission_timestamp - $last_absent_timestamp) / (60 * 60 * 24); // Convert seconds to days

        if ($date_diff > 4) {
            $status = "Auto-Rejected"; // Auto reject if more than 4 days
        }
    }

    // Insert leave application
    $query = "INSERT INTO leave_applications (student_id, document, reason, status, submission_date, deadline) 
              VALUES ('$student_id', '$target', '$reason', '$status', '$submission_date', '$submission_date')";
    mysqli_query($con, $query);

    header("Location: student_home.php?message=Leave Request Submitted");
    exit();
}

// Fetch leave history
$leave_query = "SELECT * FROM leave_applications WHERE student_id='$student_id' ORDER BY submission_date DESC";
$leave_result = mysqli_query($con, $leave_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 700px;
        }
        h2, h3 {
            color: #007bff;
            font-weight: bold;
            text-align: center;
        }
        .btn {
            border-radius: 8px;
            padding: 10px 15px;
            font-weight: bold;
            width: 100%;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .alert {
            text-align: center;
        }
        .leave-history {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #ff3b3b;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Apply for Leave</h2>

        <?php if (isset($_GET['message'])) { echo "<div class='alert alert-success'>" . $_GET['message'] . "</div>"; } ?>

        <form method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Leave</label>
                <textarea class="form-control" name="reason" required></textarea>
            </div>
            <div class="mb-3">
                <label for="document" class="form-label">Upload Supporting Document</label>
                <input type="file" class="form-control" name="document" required>
            </div>
            <button type="submit" class="btn btn-primary">Apply for Leave</button>
        </form>

        <hr>

        <h3>Leave History</h3>

        <?php if (mysqli_num_rows($leave_result) > 0) { ?>
            <table class="table table-bordered mt-3">
                <thead class="table-primary">
                    <tr>
                        <th>Reason</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th>Submission Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($leave_result)) { ?>
                        <tr>
                            <td><?php echo $row['reason']; ?></td>
                            <td>
                                <?php if (!empty($row['document'])) { ?>
                                    <a href="<?php echo $row['document']; ?>" target="_blank" class="btn btn-info btn-sm">View File</a>
                                <?php } else { ?>
                                    <span class="text-danger">No Document</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == "Pending") { ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php } elseif ($row['status'] == "Approved") { ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php } elseif ($row['status'] == "Rejected") { ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php } elseif ($row['status'] == "Auto-Rejected") { ?>
                                    <span class="badge bg-dark">Auto-Rejected</span>
                                <?php } ?>
                            </td>
                            <td><?php echo $row['submission_date']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="leave-history">No leave applications found.</p>
        <?php } ?>

        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>
</body>
</html>
