<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_data'])) {
    die("Login failed. Unauthorized access.");
}

if (!isset($_GET['file'])) {
    die("No file specified.");
}

$filename = basename($_GET['file']);  // Prevent directory traversal attacks
$file_path = "uploads/" . $filename;

if (!file_exists($file_path)) {
    die("File not found.");
}

// Serve the file for download
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
header("Content-Length: " . filesize($file_path));
readfile($file_path);
exit();
?>
