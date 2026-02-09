<?php
session_start();
require_once "../config/db.php";

// 1. Auth Guard: Ensure only logged-in users can book
 if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connect();
    
    $customer_id = $_SESSION['user_id'];
    $car_id = (int)$_POST['car_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // 2. Logic: Calculate the number of days
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    
    // Check if dates are logical
    if ($end < $start) {
        die("Critical Error: Return date cannot be before pickup date.");
    }

    $interval = $start->diff($end);
    $days = $interval->days;

    // Minimum charge is 1 day (e.g., if they return it the same day)
    if ($days == 0) {
        $days = 1;
    }

    // 3. Security: Fetch the price from the database (Do NOT trust the frontend for price)
    $price_stmt = $conn->prepare("SELECT price_per_day FROM cars WHERE id = ?");
    $price_stmt->bind_param("i", $car_id);
    $price_stmt->execute();
    $car_result = $price_stmt->get_result()->fetch_assoc();

    if (!$car_result) {
        die("Critical Error: Vehicle not found.");
    }

    $daily_rate = $car_result['price_per_day'];
    $total_price = $days * $daily_rate;

    // 4. Database Insert
    // We use 'pending' as the default status for the Owner to approve
    $stmt = $conn->prepare("INSERT INTO bookings (car_id, customer_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iissd", $car_id, $customer_id, $start_date, $end_date, $total_price);

    if ($stmt->execute()) {
        // Redirect to customer dashboard or a "My Bookings" page with success message
        header("Location: my_bookings.php?msg=success");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $price_stmt->close();
} else {
    // Redirect if they try to access this script via URL directly
    header("Location: customer_dashboard.php");
    exit;
}
?>