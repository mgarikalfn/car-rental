<?php
session_start();
require_once "../config/db.php";

// 1. Auth Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();
$booking_id = (int)$_GET['id'];
$customer_id = $_SESSION['user_id'];

// 2. Security Check: Ensure the booking belongs to this customer AND is still pending
$check_sql = "SELECT id FROM bookings WHERE id = ? AND customer_id = ? AND status = 'pending'";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // 3. Process the Cancellation
    $update_sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
    $upd_stmt = $conn->prepare($update_sql);
    $upd_stmt->bind_param("i", $booking_id);
    
    if ($upd_stmt->execute()) {
        header("Location: my_bookings.php?msg=cancelled");
    } else {
        header("Location: my_bookings.php?msg=error");
    }
} else {
    // If they try to cancel an approved/completed booking or someone else's booking
    header("Location: my_bookings.php?msg=unauthorized");
}
exit;   