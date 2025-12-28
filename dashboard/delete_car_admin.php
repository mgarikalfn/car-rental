<?php
session_start();
require_once "../config/db.php";

// 1. Auth Guard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized Access");
}

$conn = connect();
$car_id = (int)$_GET['id'];

// 2. Fetch image name before starting transaction (so we can delete file later)
$img_stmt = $conn->prepare("SELECT image FROM cars WHERE id = ?");
$img_stmt->bind_param("i", $car_id);
$img_stmt->execute();
$car_data = $img_stmt->get_result()->fetch_assoc();

if (!$car_data) {
    header("Location: admin_dashboard.php?msg=not_found");
    exit;
}

// 3. START TRANSACTION
$conn->begin_transaction();

try {
    // A. Delete all related bookings first (Children)
    // This prevents Foreign Key constraint errors
    $stmt1 = $conn->prepare("DELETE FROM bookings WHERE car_id = ?");
    $stmt1->bind_param("i", $car_id);
    $stmt1->execute();

    // B. Delete the car itself (Parent)
    $stmt2 = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt2->bind_param("i", $car_id);
    $stmt2->execute();

    // 4. COMMIT EVERYTHING
    $conn->commit();

    // 5. Clean up the physical file
    if ($car_data['image'] && file_exists("../assets/" . $car_data['image'])) {
        unlink("../assets/" . $car_data['image']);
    }

    header("Location: admin_dashboard.php?msg=deleted_successfully");

} catch (Exception $e) {
    // 6. ROLLBACK IF ANYTHING FAILS
    // If the database crashes mid-way, this undoes the partial deletion
    $conn->rollback();
    header("Location: admin_dashboard.php?msg=transaction_failed");
}
exit;