<?php
session_start();
require_once "../config/db.php";

$upload_dir = "../assets/";

// Directory maintenance
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
if (!is_writable($upload_dir)) {
    chmod($upload_dir, 0777);
}

// 1. AUTHENTICATION GUARD
$owner_id = $_SESSION['user_id'] ?? null;
if (!$owner_id || $_SESSION['user_role'] !== 'Owner') {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();

// 2. INITIALIZE VARIABLES
$errors = [];
$success = "";
$car_name = $category = $description = $price_per_day = "";
$is_available = 1;

// 3. HANDLE BOOKING ACTIONS (APPROVE/REJECT)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $booking_id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        // Get the car_id associated with this specific booking
        $stmt = $conn->prepare("SELECT car_id FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $booking_data = $stmt->get_result()->fetch_assoc();
        $car_id = $booking_data['car_id'];

        $conn->begin_transaction();
        try {
            // 1. Update booking status to approved
            $conn->query("UPDATE bookings SET status = 'approved' WHERE id = $booking_id");
            // 2. Set the car as unavailable for further bookings
            $conn->query("UPDATE cars SET is_available = 0 WHERE id = $car_id");

            $conn->commit();
            $_SESSION['msg'] = "Booking approved and car status updated!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Critical System Error: Could not process approval.";
        }
    } elseif ($action === 'reject') {
        $conn->query("UPDATE bookings SET status = 'rejected' WHERE id = $booking_id");
        $_SESSION['msg'] = "Booking request rejected.";
    }
    header("Location: owner_dashboard.php");
    exit;
}

// 4. HANDLE ADD CAR FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $car_name = trim($_POST['car_name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price_per_day = trim($_POST['price_per_day']);
    $seats = (int)$_POST['seats'];

    if (empty($seats) || $seats <= 0) $errors['seats'] = "Please specify seats.";
    if (empty($car_name)) $errors['car_name'] = "Car model name is required.";
    if (empty($category)) $errors['category'] = "Select a category.";
    if (empty($price_per_day)) $errors['price_per_day'] = "Price is required.";

    // Image Validation
    $imageName = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $imageName = uniqid("car_", true) . "." . $ext;
        } else {
            $errors['image'] = "Invalid image format.";
        }
    } else {
        $errors['image'] = "A vehicle image is required.";
    }

    if (empty($errors)) {
        $upload_path = $upload_dir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $stmt = $conn->prepare("INSERT INTO cars (owner_id, car_name, category, seats, description, price_per_day, image, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssdisi", $owner_id, $car_name, $category, $seats, $description, $price_per_day, $imageName, $is_available);
            if ($stmt->execute()) {
                $success = "Vehicle added to fleet!";
                $car_name = $category = $description = $price_per_day = "";
            } else {
                $errors['db'] = "Database error.";
            }
        }
    }
}

// 5. FETCH STATS (FOR DASHBOARD OVERVIEW)
// Update your revenue calculation to this:
$stats_sql = "SELECT 
    COUNT(id) as total_bookings,
    SUM(CASE WHEN status IN ('approved', 'completed') THEN total_price ELSE 0 END) as total_revenue,
    (SELECT COUNT(*) FROM cars WHERE owner_id = ?) as total_cars
    FROM bookings b
    WHERE b.car_id IN (SELECT id FROM cars WHERE owner_id = ?)";
$s_stmt = $conn->prepare($stats_sql);
$s_stmt->bind_param("ii", $owner_id, $owner_id);
$s_stmt->execute();
$stats = $s_stmt->get_result()->fetch_assoc();

/// 6. FETCH FLEET & BOOKINGS

// Fleet Query: This is safe because $owner_id is an integer from the session
$cars_result = $conn->query("SELECT * FROM cars WHERE owner_id = $owner_id ORDER BY created_at DESC");

// Booking Query: MUST use prepare/bind_param because of the '?' placeholder
$booking_sql = "SELECT b.*, c.car_name, u.name as customer_name 
                FROM bookings b 
                JOIN cars c ON b.car_id = c.id 
                JOIN users u ON b.customer_id = u.id 
                WHERE c.owner_id = ? 
                AND b.status != 'cancelled' 
                ORDER BY b.created_at DESC";

$b_stmt = $conn->prepare($booking_sql);
$b_stmt->bind_param("i", $owner_id);
$b_stmt->execute();
$bookings_result = $b_stmt->get_result(); // This gives you the result object for your while/foreach loop