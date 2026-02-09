<?php
session_start();
require_once "../config/db.php";

// Auth Guard - Restored and updated to match your dashboard style
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: customer_dashboard.php");
    exit;
}

$conn = connect();
$id = (int)$_GET['id'];

// Fetch car details
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND is_available = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();

if (!$car) {
    die("Vehicle not found or no longer available.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['car_name']) ?> | Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #f5b754;
            --dark-bg: #15191d;
            --soft-bg: #f8f9fa;
        }

        body { background-color: var(--soft-bg); font-family: 'Poppins', sans-serif; }

        /* Restored Sidebar/Navbar Profile Styling */
        header.dashboard-nav {
            background-color: white;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .profile-link {
            text-decoration: none;
            color: var(--dark-bg);
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .profile-link:hover { color: var(--primary-gold); }
        .profile-icon-box {
            width: 35px; height: 35px; background: #eee; 
            border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; font-size: 1.2rem;
        }

        /* Page Content Styles */
        .details__img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .spec__box {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #eee;
            transition: 0.3s;
        }
        .spec__box:hover {
            border-color: var(--primary-gold);
            transform: translateY(-5px);
        }
        .booking__card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            position: sticky;
            top: 100px;
        }
        .btn-reserve {
            background-color: var(--dark-bg);
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: white;
            transition: 0.3s;
        }
        .btn-reserve:hover {
            background-color: var(--primary-gold);
            color: var(--dark-bg);
        }
    </style>
</head>
<body>

<header class="dashboard-nav shadow-sm">
    <nav class="container d-flex justify-content-between align-items-center py-3">
        <div class="nav__logo fw-bold fs-4">
            <a href="customer_dashboard.php" class="text-dark text-decoration-none">RENTAL <span class="text-warning">FLEET</span></a>
        </div>
        
        <div class="d-flex align-items-center gap-4">
            <ul class="nav d-none d-md-flex list-unstyled m-0 gap-4">
                <li><a href="customer_dashboard.php" class="text-muted text-decoration-none small fw-bold">Explore</a></li>
                <li><a href="my_bookings.php" class="text-muted text-decoration-none small fw-bold">My Bookings</a></li>
            </ul>

            <a href="../auth/profile.php" class="profile-link">
                <div class="text-end d-none d-sm-block">
                    <p class="mb-0 small fw-bold"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                    <p class="mb-0 text-muted small" style="font-size: 0.7rem;">Profile Settings</p>
                </div>
                <div class="profile-icon-box">
                    <i class="ri-user-settings-line"></i>
                </div>
            </a>

            <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Logout</a>
        </div>
    </nav>
</header>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="customer_dashboard.php" class="text-decoration-none text-muted">Fleet</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><?= htmlspecialchars($car['car_name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-8">
            <img src="../assets/<?= htmlspecialchars($car['image']) ?>" class="details__img mb-4" alt="car">
            
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h1 class="fw-bold mb-0"><?= htmlspecialchars($car['car_name']) ?></h1>
                <div class="price-badge bg-white p-2 px-3 rounded-pill border shadow-sm">
                    <span class="fs-3 fw-bold text-dark">$<?= number_format($car['price_per_day'], 0) ?></span>
                    <span class="text-muted small">/ Day</span>
                </div>
            </div>

            <div class="row mb-5 g-3">
                <div class="col-md-4">
                    <div class="spec__box shadow-sm">
                        <i class="ri-user-line ri-2x mb-2" style="color:var(--primary-gold)"></i>
                        <p class="mb-0 small text-muted">CAPACITY</p>
                        <h5 class="mb-0 fw-bold"><?= $car['seats'] ?> Seats</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="spec__box shadow-sm">
                        <i class="ri-steering-2-line ri-2x mb-2" style="color:var(--primary-gold)"></i>
                        <p class="mb-0 small text-muted">CATEGORY</p>
                        <h5 class="mb-0 fw-bold"><?= $car['category'] ?></h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="spec__box shadow-sm">
                        <i class="ri-gas-station-line ri-2x mb-2" style="color:var(--primary-gold)"></i>
                        <p class="mb-0 small text-muted">TRANSMISSION</p>
                        <h5 class="mb-0 fw-bold">Automatic</h5>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-3">Vehicle Description</h4>
            <p class="text-muted lead mb-5" style="font-size: 1.1rem; line-height: 1.8;">
                <?= nl2br(htmlspecialchars($car['description'])) ?>
            </p>
        </div>

        <div class="col-lg-4">
            <div class="card booking__card p-4 shadow-lg">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-warning p-2 rounded-3 me-3">
                        <i class="ri-calendar-check-line fs-4 text-dark"></i>
                    </div>
                    <h4 class="fw-bold mb-0">Reserve Vehicle</h4>
                </div>
                
                <form action="process_booking.php" method="POST">
                    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">PICKUP DATE</label>
                        <input type="date" name="start_date" class="form-control py-2 rounded-3" required min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">RETURN DATE</label>
                        <input type="date" name="end_date" class="form-control py-2 rounded-3" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3">
                        <span class="text-muted">Daily Rate</span>
                        <span class="fw-bold fs-5">$<?= number_format($car['price_per_day'], 0) ?></span>
                    </div>

                    <button type="submit" class="btn btn-reserve w-100 rounded-pill py-3 shadow-sm text-uppercase">
                        Confirm Booking <i class="ri-arrow-right-line ms-2"></i>
                    </button>
                </form>
                
                <div class="mt-4 p-3 rounded-3 border-start border-4 border-warning bg-light">
                    <small class="text-muted d-block fw-bold mb-1"><i class="ri-information-line me-1"></i> Rental Policy:</small>
                    <small class="text-muted">Requests are sent to the owner for approval. Total duration determines the final price.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 py-5 bg-dark text-white text-center">
    <p class="mb-0 opacity-50">&copy; 2024 RENTAL Fleet Management. Experience Premium.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>