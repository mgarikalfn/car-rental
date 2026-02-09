<?php 
include 'owner_logic.php'; 

// Check verification status
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-gold: #f5b754;
            --dark-bg: #15191d;
            --soft-bg: #f0f2f5;
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
            border-radius: 10px; transition: 0.3s; border: 1px solid transparent;
        }
        .nav-pills-custom .nav-link.active {
            background-color: var(--dark-bg); color: var(--primary-gold);
        }

        /* Fleet Card Fixes */
        .car-image-container {
            height: 180px;
            background: #eee;
            overflow: hidden;
        }
        .car-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .table-container {
            background: white; border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d1e7dd; color: #0f5132; }
        .badge-rejected { background: #f8d7da; color: #842029; }
        .badge-returned { background: #e2e3e5; color: #41464b; }
        
        .verification-lock {
            background: white; border: 2px dashed #cbd5e0;
            border-radius: 20px; padding: 60px 20px; text-align: center;
        }
    </style>
</head>

<body>

    <header class="bg-white border-bottom sticky-top shadow-sm">
        <nav class="container d-flex justify-content-between align-items-center py-3">
            <div class="fw-bold fs-4"><a href="../index.php" class="text-dark text-decoration-none">RENTAL <span class="text-warning">OWNER</span></a></div>
            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-sm-block">
                    <p class="mb-0 small fw-bold"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    <?php if($is_verified): ?>
                        <p class="mb-0 text-success small"><i class="ri-checkbox-circle-fill"></i> Verified Partner</p>
                    <?php else: ?>
                        <p class="mb-0 text-warning small"><i class="ri-time-line"></i> Pending Verification</p>
                    <?php endif; ?>
                </div>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Logout</a>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        
        <div class="row mb-5 g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="ri-money-dollar-box-line"></i></div>
                    <h6 class="text-muted small fw-bold text-uppercase">Total Revenue</h6>
                    <h2 class="fw-bold mb-0">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="ri-car-line"></i></div>
                    <h6 class="text-muted small fw-bold text-uppercase">Fleet Size</h6>
                    <h2 class="fw-bold mb-0"><?= $stats['total_cars'] ?? 0 ?> Vehicles</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon bg-success-subtle text-success"><i class="ri-calendar-check-line"></i></div>
                    <h6 class="text-muted small fw-bold text-uppercase">Active Bookings</h6>
                    <h2 class="fw-bold mb-0"><?= $stats['total_bookings'] ?? 0 ?></h2>
                </div>
            </div>
        </div>

        <ul class="nav nav-pills nav-pills-custom mb-5 justify-content-center gap-2" id="ownerTab" role="tablist">
            <li class="nav-item"><button class="nav-link active shadow-sm" data-bs-toggle="tab" data-bs-target="#view_bookings">Requests</button></li>
            <li class="nav-item"><button class="nav-link shadow-sm" data-bs-toggle="tab" data-bs-target="#view_fleet">Manage Fleet</button></li>
            <li class="nav-item"><button class="nav-link shadow-sm" data-bs-toggle="tab" data-bs-target="#add_vehicle"><i class="ri-add-circle-line"></i> Add Vehicle</button></li>
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
                                    <th>TOTAL</th>
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
                <div class="row g-4">
                    <?php if ($cars_result->num_rows > 0): ?>
                        <?php while ($car = $cars_result->fetch_assoc()): ?>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                    <div class="car-image-container">
                                        <img src="../assets/<?= htmlspecialchars($car['image']) ?>" alt="car">
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($car['car_name']) ?></h6>
                                            <span class="badge bg-warning-subtle text-dark">$<?= number_format($car['price_per_day'], 0) ?></span>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            <i class="ri-settings-3-line"></i> <?= $car['category'] ?> | 
                                            <i class="ri-user-line"></i> <?= $car['seats'] ?> Seats
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn btn-outline-dark flex-grow-1 btn-sm rounded-pill">Edit</a>
                                            <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Remove car?')"><i class="ri-delete-bin-line"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="ri-car-washing-line display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">Your fleet is currently empty.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="add_vehicle">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <?php if ($is_verified): ?>
                            <div class="table-container shadow">
                                <h4 class="fw-bold mb-4 text-center">New Fleet Entry</h4>
                                <form action="owner_dashboard.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="add_car">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold">CAR NAME</label>
                                            <input type="text" name="car_name" class="form-control bg-light border-0 py-3" placeholder="e.g. Porsche 911 Carrera" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold">CATEGORY</label>
                                            <select name="category" class="form-select bg-light border-0 py-3" required>
                                                <option value="Luxury">Luxury</option>
                                                <option value="SUV">SUV</option>
                                                <option value="Sports">Sports</option>
                                                <option value="Electric">Electric</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">SEATS</label>
                                            <input type="number" name="seats" class="form-control bg-light border-0 py-3" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">PRICE / DAY</label>
                                            <input type="number" name="price_per_day" class="form-control bg-light border-0 py-3" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small fw-bold">IMAGE</label>
                                            <input type="file" name="image" class="form-control bg-light border-0 py-3" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-dark w-100 py-3 mt-4 rounded-pill fw-bold shadow">ADD TO FLEET</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="verification-lock">
                                <i class="ri-shield-keyhole-line text-warning display-3 mb-3 d-block"></i>
                                <h4 class="fw-bold text-dark">Awaiting Verification</h4>
                                <p class="text-muted mx-auto" style="max-width: 450px;">
                                    Your account status is <strong>Pending</strong>. For security, car owners must be verified by RENTAL Admin before listing vehicles.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($_GET['msg'])): ?>
    <script>
        const msg = "<?= $_GET['msg'] ?>";
        if (msg === 'approved') Swal.fire('Approved!', 'The booking is now active.', 'success');
        if (msg === 'returned') Swal.fire('Returned!', 'The vehicle is available again.', 'info');
        if (msg === 'rejected') Swal.fire('Rejected', 'The booking was declined.', 'error');
    </script>
    <?php endif; ?>

</body>
</html>