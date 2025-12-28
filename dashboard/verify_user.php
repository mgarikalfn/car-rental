<?php
session_start();
require_once "../config/db.php";

// Auth Guard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized");
}

$conn = connect();
$user_id = (int)$_GET['id'];
$status = (int)$_GET['status']; // 1 for verify, 0 for revoke

// Update the user record
$stmt = $conn->prepare("UPDATE users SET is_verified = ? WHERE id = ?");
$stmt->bind_param("ii", $status, $user_id);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php?msg=user_updated");
} else {
    header("Location: admin_dashboard.php?msg=error");
}
exit;