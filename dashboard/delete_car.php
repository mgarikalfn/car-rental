<?php
session_start();
require_once "../config/db.php";

// Enable MySQLi Exception reporting (This prevents the 500 error and triggers the catch block)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Owner') {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();

if (isset($_GET['id'])) {
    $car_id = (int)$_GET['id'];
    $owner_id = $_SESSION['user_id'];

    // Start Transaction
    $conn->begin_transaction();

    try {
        // 1. Fetch image name FIRST (before deleting the car)
        $img_stmt = $conn->prepare("SELECT image FROM cars WHERE id = ? AND owner_id = ?");
        $img_stmt->bind_param("ii", $car_id, $owner_id);
        $img_stmt->execute();
        $result = $img_stmt->get_result();
        $car = $result->fetch_assoc();

        if ($car) {
            // 2. Delete associated bookings
            $del_bookings = $conn->prepare("DELETE FROM bookings WHERE car_id = ?");
            $del_bookings->bind_param("i", $car_id);
            $del_bookings->execute();

            // 3. Delete the car
            $del_car = $conn->prepare("DELETE FROM cars WHERE id = ? AND owner_id = ?");
            $del_car->bind_param("ii", $car_id, $owner_id);
            $del_car->execute();

            // 4. Commit changes to Database
            $conn->commit();

            // 5. Delete physical file (Only after DB success)
            $file_path = "../assets/" . $car['image'];
            if (!empty($car['image']) && file_exists($file_path)) {
                unlink($file_path);
            }
            
            header("Location: owner_dashboard.php?msg=deleted");
            exit;
        } else {
            // Car not found or not owned by user
            $conn->rollback();
            header("Location: owner_dashboard.php?msg=not_found");
            exit;
        }

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        // Redirect with the actual error code for debugging
        header("Location: owner_dashboard.php?msg=error&details=" . urlencode($e->getMessage()));
        exit;
    }
}
?>