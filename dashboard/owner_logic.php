<?php
session_start();
$upload_dir = "../assets/";

// If the folder doesn't exist, try to create it automatically
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Ensure the folder is writable (especially on Mac/Linux)
if (!is_writable($upload_dir)) {
    chmod($upload_dir, 0777);
}
require_once "../config/db.php";

// 1. AUTHENTICATION GUARD
// Ensures only logged-in Owners can access this logic
$owner_id = $_SESSION['user_id'] ?? null;
if (!$owner_id || $_SESSION['user_role'] !== 'Owner') {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();

// 2. INITIALIZE VARIABLES
$errors = [];
$success = "";

// Sticky form variables (to keep input values after a validation error)
$car_name = $category = $description = $price_per_day = "";
$is_available = 1;

// 3. HANDLE ADD CAR FORM SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {

    // Sanitize inputs
    $car_name = trim($_POST['car_name']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price_per_day = trim($_POST['price_per_day']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $seats = (int)$_POST['seats']; // Capture seats

    // Validation
    if (empty($seats) || $seats <= 0) {
        $errors['seats'] = "Please specify number of seats.";
    }


    if (empty($car_name)) {
        $errors['car_name'] = "Car model name is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9 ]+$/", $car_name)) {
        $errors['car_name'] = "Car name can only contain letters, numbers, and spaces.";
    }

    if (empty($category)) {
        $errors['category'] = "Please select a vehicle category.";
    }

    if (empty($description)) {
        $errors['description'] = "Please provide a description of the vehicle.";
    }

    if (empty($price_per_day)) {
        $errors['price_per_day'] = "Daily rental price is required.";
    } elseif (!is_numeric($price_per_day) || $price_per_day <= 0) {
        $errors['price_per_day'] = "Please enter a valid positive price.";
    }

    // --- Image Validation & Processing ---
    $imageName = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $file_info = pathinfo($_FILES['image']['name']);
        $extension = strtolower($file_info['extension']);

        if (!in_array($extension, $allowed_extensions)) {
            $errors['image'] = "Invalid format. Only JPG, PNG, and WEBP allowed.";
        } else {
            // Generate unique filename to prevent overwriting
            $imageName = uniqid("car_", true) . "." . $extension;
        }
    } else {
        $errors['image'] = "A vehicle image is required.";
    }

    // 4. DATABASE INSERTION (Only if no errors)
    if (empty($errors)) {
        $upload_path = "../assets/" . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $stmt = $conn->prepare("INSERT INTO cars (owner_id, car_name, category, seats, description, price_per_day, image, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssdisi", $owner_id, $car_name, $category, $seats, $description, $price_per_day, $imageName, $is_available);
            if ($stmt->execute()) {
                $success = "Excellent! Your vehicle has been added to the fleet.";
                // Clear form fields on success
                $car_name = $category = $description = $price_per_day = "";
                $is_available = 1;
            } else {
                $errors['db'] = "Database error: Unable to save vehicle details.";
            }
            $stmt->close();
        } else {
            $error_code = $_FILES['image']['error'];
            $target = realpath($upload_dir);

            if (!$target) {
                $errors['image'] = "Folder Error: The path '$upload_dir' does not exist on the server.";
            } elseif (!is_writable($target)) {
                $errors['image'] = "Permission Error: The server cannot write to the folder: $target";
            } else {
                $errors['image'] = "System Error: move_uploaded_file failed. (Error Code: $error_code)";
            }
        }
    }
}

// 5. FETCH DATA FOR DASHBOARD DISPLAY
// Get all cars belonging to this owner
$cars_result = $conn->query("SELECT * FROM cars WHERE owner_id = $owner_id ORDER BY created_at DESC");

// Get bookings for this owner's cars
$bookings_query = "SELECT b.*, u.name as customer_name, c.car_name 
                   FROM bookings b 
                   JOIN users u ON b.customer_id = u.id 
                   JOIN cars c ON b.car_id = c.id 
                   WHERE c.owner_id = $owner_id 
                   ORDER BY b.created_at DESC";
$bookings_result = $conn->query($bookings_query);
