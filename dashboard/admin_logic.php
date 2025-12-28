<?php
session_start();
require_once "../config/db.php";

// 1. AUTHENTICATION GUARD
// Only "Admin" role can enter
/* if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit;
} */

$conn = connect();

// 2. FETCH PLATFORM STATS
// Total Revenue from all owners
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM cars) as total_cars,
    (SELECT SUM(total_price) FROM bookings WHERE status IN ('approved', 'completed')) as platform_revenue";
$stats = $conn->query($stats_query)->fetch_assoc();

// 3. FETCH ALL USERS (Except current admin)
// 3. FETCH ALL USERS (Make sure is_verified is included!)
$users_result = $conn->query("SELECT id, name, email, role, is_verified, created_at FROM users WHERE role != 'Admin' ORDER BY created_at DESC");
// 4. FETCH ALL BOOKINGS (Global View)
$bookings_query = "SELECT b.*, u.name as customer_name, c.car_name, o.name as owner_name 
                   FROM bookings b 
                   JOIN users u ON b.customer_id = u.id 
                   JOIN cars c ON b.car_id = c.id 
                   JOIN users o ON c.owner_id = o.id 
                   ORDER BY b.created_at DESC LIMIT 10";
$bookings_result = $conn->query($bookings_query);

// 5. FETCH GLOBAL FLEET SUMMARY
$fleet_query = "SELECT c.*, u.name as owner_name 
                FROM cars c 
                JOIN users u ON c.owner_id = u.id 
                ORDER BY c.created_at DESC";
$fleet_result = $conn->query($fleet_query);
?>