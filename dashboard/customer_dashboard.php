<?php
session_start();
require_once "../config/db.php";

// Auth Guard - Redirect if not a Customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();

// Fetch only cars that are available and not deleted
$sql = "SELECT * FROM cars WHERE is_available = 1 ORDER BY id DESC";
$result = $conn->query($sql);
$cars = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Get unique categories for filter buttons
$categories = array_unique(array_column($cars, 'category'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .dashboard-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../assets/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            border-radius: 0 0 30px 30px;
            margin-bottom: 50px;
        }
        .search-box {
            max-width: 600px;
            margin: -40px auto 40px;
            background: white;
            padding: 10px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .search-box input { border: none; outline: none; padding-left: 20px; }
        
        .filter__btn {
            padding: 0.6rem 1.2rem;
            border: 1px solid #ddd;
            background: white;
            border-radius: 50px;
            cursor: pointer;
            transition: 0.3s;
        }
        .filter__btn.active { background: #f5b754; border-color: #f5b754; font-weight: bold; }
        
        /* Layout Fixes */
        .btn__group { display: flex; gap: 10px; margin-top: 1rem; }
        .view__btn { flex: 1; border-radius: 8px; font-weight: 600; text-align: center; padding: 10px; text-decoration: none; }
        .btn-main { background: #15191d; color: white; }
        .btn-accent { background: #f5b754; color: #15191d; border: none; }
        .btn-accent:hover { background: #15191d; color: white; }
        
        .hidden { display: none; }
    </style>
</head>
<body class="bg-light">

<header class="dashboard-header text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Hello, <?= explode(' ', $_SESSION['user_name'])[0] ?>!</h1>
        <p class="lead">Where would you like to go today?</p>
    </div>
</header>

<div class="container">
    <div class="search-box d-flex align-items-center">
        <i class="ri-search-line fs-4 ms-3 text-muted"></i>
        <input type="text" id="carSearch" class="form-control" placeholder="Search car name..." onkeyup="searchCars()">
    </div>

    <div class="d-flex justify-content-center gap-2 mb-5">
        <button class="filter__btn active" onclick="filterCars('all', this)">All</button>
        <?php foreach($categories as $cat): ?>
            <button class="filter__btn" onclick="filterCars('<?= $cat ?>', this)"><?= $cat ?></button>
        <?php endforeach; ?>
    </div>

    <div class="range__grid" id="carGrid">
        <?php foreach($cars as $car): ?>
        <div class="range__card" data-category="<?= $car['category'] ?>" data-name="<?= strtolower($car['car_name']) ?>">
            <img src="../assets/<?= $car['image'] ?>" alt="car">
            <div class="range__details">
                <div class="d-flex justify-content-between align-items-start">
                    <h4><?= $car['car_name'] ?></h4>
                    <span class="badge bg-light text-dark border"><?= $car['category'] ?></span>
                </div>
                <div class="car__specs my-2 text-muted small">
                    <span><i class="ri-user-line"></i> <?= $car['seats'] ?> Seats</span>
                    <span><i class="ri-flashlight-line"></i> Automatic</span>
                </div>
                <div class="price__tag mt-2">$<?= number_format($car['price_per_day'], 2) ?><small>/day</small></div>
                
                <div class="btn__group">
                    <a href="car_details.php?id=<?= $car['id'] ?>" class="view__btn btn-main">Details</a>
                    <button class="view__btn btn-accent" data-bs-toggle="modal" data-bs-target="#quickBook<?= $car['id'] ?>">Book</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="quickBook<?= $car['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="process_booking.php" method="POST">
                        <div class="modal-body p-4 pt-0 text-center">
                            <img src="../assets/<?= $car['image'] ?>" class="img-fluid rounded mb-3" style="max-height: 150px;">
                            <h4 class="fw-bold"><?= $car['car_name'] ?></h4>
                            <p class="text-muted small mb-4">Daily Rate: $<?= number_format($car['price_per_day'], 2) ?></p>
                            
                            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                            <div class="row g-3">
                                <div class="col-6 text-start">
                                    <label class="small fw-bold">PICKUP</label>
                                    <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-6 text-start">
                                    <label class="small fw-bold">RETURN</label>
                                    <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold">Confirm Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<footer class="mt-5 py-5 bg-dark text-white text-center">
    <p>&copy; 2024 RENTAL Fleet Management. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Search Function
    function searchCars() {
        let input = document.getElementById('carSearch').value.toLowerCase();
        let cards = document.querySelectorAll('.range__card');
        
        cards.forEach(card => {
            let name = card.getAttribute('data-name');
            if(name.includes(input)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // Filter Function
    function filterCars(category, btn) {
        document.querySelectorAll('.filter__btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        let cards = document.querySelectorAll('.range__card');
        cards.forEach(card => {
            if (category === 'all' || card.getAttribute('data-category') === category) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>
</body>
</html>