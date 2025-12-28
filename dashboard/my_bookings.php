<?php
session_start();
require_once "../config/db.php";

// Auth Guard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();
$customer_id = $_SESSION['user_id'];

// SQL: Join bookings with cars to get the name and image
$sql = "SELECT b.*, c.car_name, c.image, c.category 
        FROM bookings b 
        JOIN cars c ON b.car_id = c.id 
        WHERE b.customer_id = ? 
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings | RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .booking-row {
            background: white;
            border-radius: 15px;
            margin-bottom: 15px;
            padding: 20px;
            transition: 0.3s;
            border: 1px solid #eee;
        }
        .booking-row:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .car-img-sm {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }
        /* Status Badge Colors */
        .badge-pending { background-color: #fff3cd; color: #856404; }
        .badge-approved { background-color: #d1e7dd; color: #0f5132; }
        .badge-rejected { background-color: #f8d7da; color: #842029; }
        .badge-cancelled { background-color: #e2e3e5; color: #41464b; }
    </style>
</head>
<body class="bg-light">

<header>
    <nav>
        <div class="nav__header">
            <div class="nav__logo"><a href="customer_dashboard.php">RENTAL</a></div>
        </div>
        <ul class="nav__links">
            <li><a href="customer_dashboard.php">Browse Cars</a></li>
            <li><a href="../auth/logout.php" class="btn">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Booking History</h2>
        <a href="customer_dashboard.php" class="btn btn-outline-dark btn-sm rounded-pill">
            <i class="ri-add-line"></i> New Booking
        </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i> 
            <strong>Booking Request Sent!</strong> The owner will review your request shortly.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (count($bookings) > 0): ?>
        <?php foreach($bookings as $b): ?>
            <div class="booking-row d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                    <img src="../assets/<?= $b['image'] ?>" class="car-img-sm" alt="">
                    <div>
                        <h6 class="fw-bold mb-0"><?= $b['car_name'] ?></h6>
                        <small class="text-muted"><?= $b['category'] ?></small>
                    </div>
                </div>

                <div class="text-md-center mb-3 mb-md-0">
                    <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem;">Rental Period</small>
                    <span><?= date('M d', strtotime($b['start_date'])) ?> — <?= date('M d, Y', strtotime($b['end_date'])) ?></span>
                </div>

                <div class="text-md-center mb-3 mb-md-0">
                    <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem;">Total Paid</small>
                    <span class="fw-bold text-dark">$<?= number_format($b['total_price'], 2) ?></span>
                </div>

                <div class="text-md-end">
                    <span class="badge rounded-pill px-3 py-2 badge-<?= $b['status'] ?>">
                        <i class="ri-checkbox-blank-circle-fill me-1" style="font-size: 0.5rem;"></i>
                        <?= ucfirst($b['status']) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="ri-calendar-line fs-1 text-muted"></i>
            <p class="mt-3 text-muted">You have no booking requests yet.</p>
            <a href="customer_dashboard.php" class="btn btn-dark rounded-pill px-4">Find a Car</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>