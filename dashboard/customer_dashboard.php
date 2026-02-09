<?php
session_start();
require_once "../config/db.php";

// Auth Guard - Redirect if not a Customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();

// Fetch only cars that are available
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
    <style>
        :root {
            --primary-gold: #f5b754;
            --dark-bg: #15191d;
            --soft-bg: #f8f9fa;
        }

        body { background-color: var(--soft-bg); font-family: 'Poppins', sans-serif; }

        /* Navbar */
        .dashboard-nav { background: white; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; }
        
        /* Restored Profile Link Styling */
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

        /* Header Section */
        .dashboard-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=1500');
            background-size: cover; background-position: center;
            color: white; padding: 80px 0 120px; border-radius: 0 0 40px 40px;
        }

        /* Search & Filters */
        .search-box {
            max-width: 700px; margin: -45px auto 40px; background: white;
            padding: 10px; border-radius: 50px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            z-index: 10; position: relative;
        }
        .search-box input { border: none; outline: none; padding-left: 15px; font-size: 1.1rem; }

        .filter__btn {
            padding: 0.6rem 1.5rem; border: 1px solid #ddd; background: white;
            border-radius: 50px; cursor: pointer; transition: 0.3s; font-weight: 500;
        }
        .filter__btn.active { background: var(--dark-bg); border-color: var(--dark-bg); color: var(--primary-gold); }

        /* Car Card Styling */
        .car-card { background: white; border-radius: 20px; overflow: hidden; transition: 0.3s; border: none; height: 100%; }
        .car-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .car-img-box { height: 200px; overflow: hidden; background: #f0f0f0; }
        .car-img-box img { width: 100%; height: 100%; object-fit: cover; }

        .price-badge { background: var(--primary-gold); color: var(--dark-bg); font-weight: 800; padding: 5px 15px; border-radius: 10px; }
        .btn-main { background: var(--dark-bg); color: white; border-radius: 10px; transition: 0.3s; }
        .btn-accent { background: var(--primary-gold); color: var(--dark-bg); border-radius: 10px; font-weight: 600; }

        .hidden { display: none; }
    </style>
</head>

<body>

    <header class="dashboard-nav shadow-sm">
        <nav class="container d-flex justify-content-between align-items-center py-3">
            <a href="customer_dashboard.php" class="fw-bold fs-4 text-dark text-decoration-none">RENTAL <span class="text-warning">FLEET</span></a>
            
            <div class="d-flex align-items-center gap-4">
                <ul class="nav d-none d-md-flex list-unstyled m-0 gap-3">
                    <li><a href="my_bookings.php" class="text-dark text-decoration-none small fw-bold">My Bookings</a></li>
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

    <header class="dashboard-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Welcome, <?= explode(' ', $_SESSION['user_name'] ?? 'Guest')[0] ?>!</h1>
            <p class="lead opacity-75">Your journey to luxury starts with a single click.</p>
        </div>
    </header>

    <div class="container">
        <div class="search-box d-flex align-items-center">
            <i class="ri-search-line fs-4 ms-3 text-muted"></i>
            <input type="text" id="carSearch" class="form-control" placeholder="Search by car name..." onkeyup="searchCars()">
        </div>

        <div class="d-flex justify-content-center flex-wrap gap-2 mb-5">
            <button class="filter__btn active" onclick="filterCars('all', this)">All Vehicles</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter__btn" onclick="filterCars('<?= $cat ?>', this)"><?= $cat ?></button>
            <?php endforeach; ?>
        </div>

        <div class="row g-4" id="carGrid">
            <?php foreach ($cars as $car): ?>
                <div class="col-md-6 col-lg-4 car-item" data-category="<?= $car['category'] ?>" data-name="<?= strtolower($car['car_name']) ?>">
                    <div class="card car-card shadow-sm">
                        <div class="car-img-box">
                            <img src="../assets/<?= htmlspecialchars($car['image']) ?>" alt="<?= $car['car_name'] ?>">
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($car['car_name']) ?></h5>
                                <span class="badge bg-light text-dark small border"><?= $car['category'] ?></span>
                            </div>
                            
                            <div class="mb-3 text-muted small">
                                <span class="me-3"><i class="ri-user-line me-1"></i> <?= $car['seats'] ?> Seats</span>
                                <span><i class="ri-flashlight-line me-1"></i> Automatic</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-badge">$<?= number_format($car['price_per_day'], 0) ?>/day</div>
                                <div class="d-flex gap-2">
                                    <a href="car_details.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-main px-3">Details</a>
                                    <button class="btn btn-sm btn-accent px-3" data-bs-toggle="modal" data-bs-target="#quickBook<?= $car['id'] ?>">Book Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="quickBook<?= $car['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0" style="border-radius: 25px;">
                            <form action="process_booking.php" method="POST">
                                <div class="modal-body p-4 text-center">
                                    <h4 class="fw-bold mb-3">Reserve Vehicle</h4>
                                    <img src="../assets/<?= htmlspecialchars($car['image']) ?>" class="img-fluid rounded mb-3" style="max-height: 150px;">
                                    <p class="fw-bold mb-4"><?= $car['car_name'] ?></p>
                                    
                                    <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                                    <div class="row g-3">
                                        <div class="col-6 text-start">
                                            <label class="small fw-bold">PICKUP DATE</label>
                                            <input type="date" name="start_date" class="form-control rounded-3" required min="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="col-6 text-start">
                                            <label class="small fw-bold">RETURN DATE</label>
                                            <input type="date" name="end_date" class="form-control rounded-3" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 p-4 pt-0">
                                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow">Confirm Booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="mt-5 py-5 bg-dark text-white text-center">
        <p class="mb-0 opacity-50">&copy; 2024 RENTAL Fleet Management.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchCars() {
            let input = document.getElementById('carSearch').value.toLowerCase();
            let items = document.querySelectorAll('.car-item');
            items.forEach(item => {
                let name = item.getAttribute('data-name');
                item.classList.toggle('hidden', !name.includes(input));
            });
        }

        function filterCars(category, btn) {
            document.querySelectorAll('.filter__btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            let items = document.querySelectorAll('.car-item');
            items.forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>