<?php
session_start();
require_once "../config/db.php";

// 1. Auth Guard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Owner') {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();

// 2. Validate Inputs
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: owner_dashboard.php");
    exit;
}

$booking_id = (int)$_GET['id'];
$action = $_GET['action'];
$owner_id = $_SESSION['user_id'];

// 3. Verify Ownership
// Security check: Make sure this booking actually belongs to a car owned by this user
$check_sql = "SELECT b.id, b.car_id FROM bookings b 
              JOIN cars c ON b.car_id = c.id 
              WHERE b.id = ? AND c.owner_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $booking_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Unauthorized: You do not have permission to manage this booking.");
}

$booking = $result->fetch_assoc();
$car_id = $booking['car_id'];

// 4. Process Actions
if ($action === 'approve') {
    // Start Transaction: Both the booking and the car status must update together
    $conn->begin_transaction();

    try {
        // Update 1: Set booking to approved
        $upd_booking = $conn->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
        $upd_booking->bind_param("i", $booking_id);
        $upd_booking->execute();

        // Update 2: Set car to UNAVAILABLE
        $upd_car = $conn->prepare("UPDATE cars SET is_available = 0 WHERE id = ?");
        $upd_car->bind_param("i", $car_id);
        $upd_car->execute();

        $conn->commit();
        header("Location: owner_dashboard.php?msg=approved");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: owner_dashboard.php?msg=error");
        exit;
    }
} 

elseif ($action === 'reject') {
    $upd_reject = $conn->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
    $upd_reject->bind_param("i", $booking_id);
    
    if ($upd_reject->execute()) {
        header("Location: owner_dashboard.php?msg=rejected");
    } else {
        header("Location: owner_dashboard.php?msg=error");
    }
    exit;
}

else {
    header("Location: owner_dashboard.php");
    exit;
}