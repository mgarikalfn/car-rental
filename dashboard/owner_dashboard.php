<?php include 'owner_logic.php'; ?>
<?php
// Check verification status from the database (Safety check)
$owner_id = $_SESSION['user_id'];
$user_check = $conn->query("SELECT is_verified FROM users WHERE id = $owner_id")->fetch_assoc();
$is_verified = $user_check['is_verified'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Command Center | RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">

    <style>
        :root {
            --primary-gold: #f5b754;
            --dark-bg: #15191d;
            --soft-bg: #f8f9fa;
        }

        body { background-color: var(--soft-bg); font-family: 'Inter', sans-serif; }

        /* Stats Cards */
        .stat-card {
            background: white; border: none; border-radius: 15px; padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); transition: 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 15px;
        }

        /* Tabs Styling */
        .nav-pills-custom .nav-link {
            color: #6c757d; font-weight: 600; padding: 12px 25px;
            border-radius: 10px; transition: 0.3s;
        }
        .nav-pills-custom .nav-link.active {
            background-color: var(--dark-bg); color: var(--primary-gold);
        }

        /* Table & List Styling */
        .table-container {
            background: white; border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d1e7dd; color: #0f5132; }
        .badge-rejected { background: #f8d7da; color: #842029; }
        .badge-completed { background-color: #e2e3e5; color: #41464b; border: 1px solid #d3d6d8; }
        
        /* Verification Overlay */
        .verification-lock {
            background: rgba(255, 255, 255, 0.9);
            border: 2px dashed #ddd;
            border-radius: 20px;
            padding: 60px 20px;
            text-align: center;
        }
    </style>
</head>

<body>

    <header class="bg-white border-bottom sticky-top">
        <nav class="container d-flex justify-content-between align-items-center py-3">
            <div class="nav__logo fw-bold fs-4"><a href="#" class="text-dark text-decoration-none">RENTAL <span class="text-warning">OWNER</span></a></div>
            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-sm-block">
                    <p class="mb-0 small fw-bold"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    <?php if($is_verified): ?>
                        <p class="mb-0 text-success small"><i class="ri-checkbox-circle-fill"></i> Verified Partner</p>
                    <?php else: ?>
                        <p class="mb-0 text-warning small"><i class="ri-time-line"></i> Pending Verification</p>
                    <?php endif; ?>
                </div>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-info alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0" role="alert">
                <i class="ri-information-line me-2"></i>
                <?php
                if ($_GET['msg'] == 'approved') echo "Booking has been <strong>Approved</strong>. The car is now reserved.";
                if ($_GET['msg'] == 'returned') echo "Vehicle marked as <strong>Returned</strong>. It is now available for new bookings.";
                if ($_GET['msg'] == 'rejected') echo "Booking has been <strong>Rejected</strong>.";
                if ($_GET['msg'] == 'error') echo "An error occurred. Please try again.";
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-5 g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-money-dollar-box-line"></i></div>
                    <h6 class="text-muted small fw-bold text-uppercase">Total Revenue</h6>
                    <h2 class="fw-bold mb-0">$<?= number_format($stats['total_revenue'], 2) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-car-line"></i></div>
                    <h6 class="text-muted small fw-bold text-uppercase">Fleet Size</h6>
                    <h2 class="fw-bold mb-0"><?= $stats['total_cars'] ?> Vehicles</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-calendar-check-line"></i></div>
                    <h6 class="text-muted small fw-bold text-uppercase">Total Bookings</h6>
                    <h2 class="fw-bold mb-0"><?= $stats['total_bookings'] ?></h2>
                </div>
            </div>
        </div>

        <ul class="nav nav-pills nav-pills-custom mb-4 justify-content-center" id="ownerTab" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#view_bookings">Requests</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#view_fleet">Manage Fleet</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#add_vehicle">Add Vehicle</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="view_bookings">
                <div class="table-container">
                    <h4 class="fw-bold mb-4">Incoming Requests</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="text-muted small">
                                <tr>
                                    <th>CUSTOMER</th>
                                    <th>VEHICLE</th>
                                    <th>PERIOD</th>
                                    <th>EARNINGS</th>
                                    <th>STATUS</th>
                                    <th class="text-end">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($bookings_result->num_rows > 0): ?>
                                    <?php while ($b = $bookings_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><div class="fw-bold text-dark"><?= htmlspecialchars($b['customer_name']) ?></div></td>
                                            <td><?= htmlspecialchars($b['car_name']) ?></td>
                                            <td><small class="text-muted"><?= date('M d', strtotime($b['start_date'])) ?> - <?= date('M d', strtotime($b['end_date'])) ?></small></td>
                                            <td class="fw-bold text-dark">$<?= number_format($b['total_price'], 2) ?></td>
                                            <td><span class="badge rounded-pill px-3 py-2 badge-<?= $b['status'] ?>"><?= ucfirst($b['status']) ?></span></td>
                                            <td class="text-end">
                                                <?php if ($b['status'] == 'pending'): ?>
                                                    <a href="update_booking.php?id=<?= $b['id'] ?>&action=approve" class="btn btn-sm btn-success rounded-pill px-3">Approve</a>
                                                    <a href="update_booking.php?id=<?= $b['id'] ?>&action=reject" class="btn btn-sm btn-outline-danger rounded-pill px-3">Reject</a>
                                                <?php elseif ($b['status'] == 'approved'): ?>
                                                    <a href="update_booking.php?id=<?= $b['id'] ?>&action=return" class="btn btn-sm btn-primary rounded-pill px-3">Mark Returned</a>
                                                <?php else: ?>
                                                    <span class="text-muted small">No actions</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No booking requests found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="view_fleet">
                <div class="range__grid">
                    <?php if ($cars_result->num_rows > 0): ?>
                        <?php while ($car = $cars_result->fetch_assoc()): ?>
                            <div class="range__card border-0 shadow-sm p-3">
                                <img src="../assets/<?= $car['image'] ?>" alt="car" class="car__img mb-3">
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($car['car_name']) ?></h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-light text-muted fw-normal"><?= $car['category'] ?></span>
                                    <span class="fw-bold text-dark">$<?= number_format($car['price_per_day'], 2) ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn btn-outline-dark flex-grow-1 btn-sm rounded-pill">Edit</a>
                                    <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Remove car?')"><i class="ri-delete-bin-line"></i></a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">Your fleet is empty.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="add_vehicle">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <?php if ($is_verified): ?>
                            <div class="table-container">
                                <h4 class="fw-bold mb-4 text-center">New Fleet Entry</h4>
                                <form action="owner_dashboard.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="add_car">
                                    <div class="row g-3">
                                        <div class="col-md-12"><label class="form-label small fw-bold text-muted">CAR NAME</label><input type="text" name="car_name" class="form-control bg-light border-0 py-3 rounded-3" placeholder="e.g. Tesla Model S" required></div>
                                        <div class="col-md-6"><label class="form-label small fw-bold text-muted">CATEGORY</label><select name="category" class="form-select bg-light border-0 py-3 rounded-3" required>
                                                <option value="Luxury">Luxury</option>
                                                <option value="SUV">SUV</option>
                                                <option value="Sports">Sports</option>
                                            </select></div>
                                        <div class="col-md-3"><label class="form-label small fw-bold text-muted">SEATS</label><input type="number" name="seats" class="form-control bg-light border-0 py-3 rounded-3" required></div>
                                        <div class="col-md-3"><label class="form-label small fw-bold text-muted">DAILY PRICE ($)</label><input type="number" name="price_per_day" class="form-control bg-light border-0 py-3 rounded-3" required></div>
                                        <div class="col-md-12"><label class="form-label small fw-bold text-muted">DESCRIPTION</label><textarea name="description" class="form-control bg-light border-0 rounded-3" rows="3"></textarea></div>
                                        <div class="col-md-12"><label class="form-label small fw-bold text-muted">CAR IMAGE</label><input type="file" name="image" class="form-control bg-light border-0 py-3 rounded-3" required></div>
                                    </div>
                                    <button type="submit" class="btn btn-dark w-100 py-3 mt-4 rounded-pill fw-bold shadow">REGISTER VEHICLE</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="verification-lock shadow-sm">
                                <i class="ri-shield-user-line text-warning display-4 mb-3 d-block"></i>
                                <h4 class="fw-bold text-dark">Account Verification Required</h4>
                                <p class="text-muted mx-auto" style="max-width: 450px;">
                                    Your account is currently under review by our administration team. 
                                    You will be able to add vehicles to the fleet once your identity is verified.
                                </p>
                                <button class="btn btn-outline-secondary rounded-pill px-4 mt-3" disabled>Verification Pending...</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>