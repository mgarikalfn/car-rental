<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized");
}

$conn = connect();
$target_id = (int)$_GET['id'];
$current_admin = $_SESSION['user_id'];

// Prevent admin from deleting themselves
if ($target_id === $current_admin) {
    header("Location: admin_dashboard.php?msg=self_delete_error");
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $target_id);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php?msg=user_deleted");
} else {
    header("Location: admin_dashboard.php?msg=error");
}
exit;