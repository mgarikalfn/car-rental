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

// Get unique categories for the filter buttons
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
        /* Filter Styles */
        .filter__container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .filter__btn {
            padding: 0.75rem 1.5rem;
            outline: none;
            border: 2px solid #15191d;
            background-color: transparent;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .filter__btn.active,
        .filter__btn:hover {
            background-color: #f5b754;
            border-color: #f5b754;
        }

        /* Animated Card Styles */
        .range__grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .range__card {
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid #f1f1f1;
        }

        .range__card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .range__card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: 0.5s;
        }

        .range__card:hover img {
            transform: scale(1.05);
        }

        .range__details {
            padding: 1.5rem;
        }

        .range__details h4 {
            font-size: 1.25rem;
            color: #15191d;
            margin-bottom: 0.5rem;
        }

        .car__specs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        .price__tag {
            font-size: 1.2rem;
            font-weight: 700;
            color: #15191d;
        }

        /* Button Group Logic */
        .btn__group {
            display: flex;
            gap: 10px;
            margin-top: 1.5rem;
        }

        .view__btn {
            display: inline-block;
            flex: 1;
            text-align: center;
            padding: 0.8rem;
            background-color: #15191d;
            color: #ffffff;
            text-decoration: none !important;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
        }

        .view__btn:hover {
            background-color: #333;
            color: #ffffff;
        }

        /* Specific Quick Book Style to allow hover */
        .btn__quick {
            background-color: #f5b754;
            color: #15191d;
        }

        .btn__quick:hover {
            background-color: #15191d;
            color: #ffffff;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <header>
        <nav>
            <div class="nav__header">
                <div class="nav__logo"><a href="#">RENTAL</a></div>
            </div>
            <ul class="nav__links">
                <li><a href="customer_dashboard.php">Home</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="../auth/logout.php" class="btn">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="section__container">
        <h2 class="section__header">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
        <p class="section__description text-center mb-5">Select a vehicle that suits your journey.</p>

        <div class="filter__container">
            <button class="filter__btn active" onclick="filterCars('all', this)">All Cars</button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter__btn" onclick="filterCars('<?= $cat ?>', this)"><?= htmlspecialchars($cat) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="range__grid">
            <?php if (count($cars) > 0): ?>
                <?php foreach ($cars as $car): ?>
                    <div class="range__card" data-category="<?= htmlspecialchars($car['category']) ?>">
                        <img src="../assets/<?= htmlspecialchars($car['image']) ?>" alt="car">
                        <div class="range__details">
                            <h4><?= htmlspecialchars($car['car_name']) ?></h4>
                            <div class="car__specs">
                                <span><i class="ri-user-line"></i> <?= $car['seats'] ?> Seats</span>
                                <span><i class="ri-steering-2-line"></i> <?= $car['category'] ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price__tag">$<?= number_format($car['price_per_day'], 2) ?><small>/day</small></span>
                            </div>
                            
                            <div class="btn__group">
                                <a href="car_details.php?id=<?= $car['id'] ?>" class="view__btn">Details</a>
                                <button class="view__btn btn__quick"
                                    data-bs-toggle="modal"
                                    data-bs-target="#quickBook<?= $car['id'] ?>">
                                    Quick Book
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="quickBook<?= $car['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius: 15px; border: none;">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold">Quick Reserve: <?= htmlspecialchars($car['car_name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="process_booking.php" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

                                        <div class="row g-3">
                                            <div class="col-md-6 text-start">
                                                <label class="form-label small fw-bold">PICKUP DATE</label>
                                                <input type="date" name="start_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                                            </div>
                                            <div class="col-md-6 text-start">
                                                <label class="form-label small fw-bold">RETURN DATE</label>
                                                <input type="date" name="end_date" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                            </div>
                                        </div>

                                        <div class="mt-4 p-3 bg-light rounded text-center">
                                            <span class="text-muted">Rate:</span>
                                            <span class="fw-bold">$<?= number_format($car['price_per_day'], 2) ?> / day</span>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="submit" class="btn btn-dark w-100 py-3 rounded-3 fw-bold">CONFIRM BOOKING</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center w-100">No cars available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function filterCars(category, btn) {
            document.querySelectorAll('.filter__btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cards = document.querySelectorAll('.range__card');
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