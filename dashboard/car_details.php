<?php
session_start();
require_once "../config/db.php";

/* if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit;
} */

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
    <link rel="stylesheet" href="../css/styles.css">
    
    <style>
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
        }
        .booking__card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            position: sticky;
            top: 100px;
        }
        .btn-dark {
            background-color: #15191d;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        .btn-dark:hover {
            background-color: #f5b754;
            color: #15191d;
        }
    </style>
</head>
<body class="bg-light">

<header>
    <nav>
        <div class="nav__header">
            <div class="nav__logo"><a href="#">RENTAL</a></div>
        </div>
        <ul class="nav__links">
            <li><a href="customer_dashboard.php">Back to Fleet</a></li>
            <li><a href="../auth/logout.php" class="btn">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="container py-5 mt-5">
    <div class="row g-5">
        <div class="col-lg-8">
            <img src="../assets/<?= htmlspecialchars($car['image']) ?>" class="details__img mb-4" alt="car">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold"><?= htmlspecialchars($car['car_name']) ?></h1>
                <h3 class="text-muted fw-light">$<?= number_format($car['price_per_day'], 2) ?> / Day</h3>
            </div>

            <div class="row mb-5 g-2">
                <div class="col-md-4">
                    <div class="spec__box">
                        <i class="ri-user-line ri-2x mb-2" style="color:#f5b754"></i>
                        <p class="mb-0 small text-muted">CAPACITY</p>
                        <h5 class="mb-0"><?= $car['seats'] ?> Seats</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="spec__box">
                        <i class="ri-steering-2-line ri-2x mb-2" style="color:#f5b754"></i>
                        <p class="mb-0 small text-muted">CATEGORY</p>
                        <h5 class="mb-0"><?= $car['category'] ?></h5>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-3">Description</h4>
            <p class="text-muted lead mb-5"><?= nl2br(htmlspecialchars($car['description'])) ?></p>
        </div>

        <div class="col-lg-4">
            <div class="card booking__card p-4">
                <h4 class="fw-bold mb-4">Book This Vehicle</h4>
                
                <form action="process_booking.php" method="POST">
                    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">PICKUP DATE</label>
                        <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">RETURN DATE</label>
                        <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Rate Per Day</span>
                        <span class="fw-bold">$<?= number_format($car['price_per_day'], 2) ?></span>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-3">
                        RESERVE NOW <i class="ri-arrow-right-line ms-2"></i>
                    </button>
                </form>
                
                <div class="mt-4 p-3 rounded-3 bg-light border">
                    <small class="text-muted d-block"><i class="ri-information-line me-1"></i> Note:</small>
                    <small class="text-muted">The total price and rental duration will be calculated on the next screen.</small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>