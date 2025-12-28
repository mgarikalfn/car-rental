<?php
session_start();
require_once "../config/db.php";
$conn = connect();

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: owner_dashboard.php");
    exit;
}

$booking_id = (int)$_GET['id'];
$action = $_GET['action'];

if ($action === 'approve') {
    // 1. Get the car_id associated with this booking
    $stmt = $conn->prepare("SELECT car_id FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $car_id = $booking['car_id'];

    // 2. Use a Transaction to ensure both updates happen together
    $conn->begin_transaction();

    try {
        // Set booking to approved
        $upd1 = $conn->prepare("UPDATE bookings SET status = 'approved' WHERE id = ?");
        $upd1->bind_param("i", $booking_id);
        $upd1->execute();

        // Set car to unavailable
        $upd2 = $conn->prepare("UPDATE cars SET is_available = 0 WHERE id = ?");
        $upd2->bind_param("i", $car_id);
        $upd2->execute();

        $conn->commit();
        header("Location: owner_dashboard.php?msg=approved");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: owner_dashboard.php?msg=error");
    }

} elseif ($action === 'reject') {
    $upd = $conn->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
    $upd->bind_param("i", $booking_id);
    $upd->execute();
    header("Location: owner_dashboard.php?msg=rejected");
}
?>