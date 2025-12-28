<?php include "admin_logic.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        :root {
            --primary-gold: #f5b754;
            --dark-bg: #15191d;
        }

        header.dashboard-nav {
            background-color: white;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .hero-banner {
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('../assets/admin-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            border-radius: 0 0 30px 30px;
            margin-bottom: 40px;
        }

        .stat-card {
            border: none;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .icon-box {
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; font-size: 1.5rem;
        }
        
        .alert-custom {
            border: none;
            border-left: 5px solid;
            border-radius: 12px;
        }
    </style>
</head>

<body class="bg-light">

    <header class="dashboard-nav">
        <nav class="container d-flex justify-content-between align-items-center py-3">
            <div class="nav__logo fw-bold fs-4">
                <a href="admin_dashboard.php" class="text-dark text-decoration-none">RENTAL <span class="text-warning">ADMIN</span></a>
            </div>

            <ul class="nav__links d-none d-md-flex list-unstyled m-0 gap-4">
                <li><a href="admin_dashboard.php" class="text-dark text-decoration-none fw-bold">Overview</a></li>
                <li><a href="#users" class="text-muted text-decoration-none">Users</a></li>
                <li><a href="#fleet" class="text-muted text-decoration-none">Fleet</a></li>
            </ul>

            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-sm-block">
                    <p class="mb-0 small fw-bold">System Admin</p>
                    <p class="mb-0 text-muted small"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
                </div>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
            </div>
        </nav>
    </header>

    <div class="hero-banner text-center">
        <div class="container">
            <h1 class="display-5 fw-bold">Control Panel</h1>
            <p class="lead">Monitoring the pulse of your rental platform.</p>
        </div>
    </div>

    <div class="container pb-5">
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-custom bg-white shadow-sm alert-dismissible fade show mb-5 border-warning" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ri-information-fill fs-4 me-3 text-warning"></i>
                    <div>
                        <?php
                            if($_GET['msg'] == 'deleted_successfully') echo "<strong>Success:</strong> Vehicle and its entire booking history have been removed.";
                            if($_GET['msg'] == 'transaction_failed') echo "<strong>Error:</strong> Critical failure during deletion. No data was changed.";
                            if($_GET['msg'] == 'user_updated') echo "<strong>Updated:</strong> Owner verification status has been changed.";
                            if($_GET['msg'] == 'error') echo "<strong>Error:</strong> A system error occurred.";
                        ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">TOTAL USERS</p>
                        <h3 class="fw-bold mb-0"><?= $stats['total_users'] ?></h3>
                    </div>
                    <div class="icon-box bg-primary-subtle text-primary"><i class="ri-group-line"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">PLATFORM CARS</p>
                        <h3 class="fw-bold mb-0"><?= $stats['total_cars'] ?></h3>
                    </div>
                    <div class="icon-box bg-warning-subtle text-warning"><i class="ri-car-fill"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1">TOTAL REVENUE</p>
                        <h3 class="fw-bold mb-0 text-success">$<?= number_format($stats['platform_revenue'], 2) ?></h3>
                    </div>
                    <div class="icon-box bg-success-subtle text-success"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
            </div>
        </div>

        <div id="users" class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
            <div class="card-header bg-white py-4 px-4 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">User Management</h4>
                <span class="badge bg-light text-dark border rounded-pill px-3">Filter: All Users</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">User Details</th>
                            <th>Role</th>
                            <th>Verification</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 <?= $user['role'] === 'Owner' ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' ?>">
                                        <?= $user['role'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['role'] === 'Owner'): ?>
                                        <?php if ($user['is_verified']): ?>
                                            <span class="text-success small fw-bold"><i class="ri-checkbox-circle-fill"></i> Verified</span>
                                        <?php else: ?>
                                            <span class="text-warning small fw-bold"><i class="ri-time-line"></i> Pending</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if ($user['role'] === 'Owner' && !$user['is_verified']): ?>
                                        <a href="verify_user.php?id=<?= $user['id'] ?>&status=1" class="btn btn-sm btn-success rounded-pill px-3">Verify</a>
                                    <?php elseif ($user['role'] === 'Owner' && $user['is_verified']): ?>
                                        <a href="verify_user.php?id=<?= $user['id'] ?>&status=0" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Revoke</a>
                                    <?php endif; ?>

                                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm text-danger ms-2" onclick="return confirm('Delete this user account?')">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="fleet" class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
            <div class="card-header bg-white py-4 px-4">
                <h4 class="mb-0 fw-bold">Global Fleet Inventory</h4>
                <p class="text-muted small mb-0">Total visibility of all listed vehicles</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Vehicle</th>
                            <th>Owner</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Purge Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($fleet_result->num_rows > 0): ?>
                            <?php while ($car = $fleet_result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="../assets/<?= $car['image'] ?>" class="rounded-3 me-3" style="width: 50px; height: 35px; object-fit: cover;">
                                            <div class="fw-bold"><?= htmlspecialchars($car['car_name']) ?></div>
                                        </div>
                                    </td>
                                    <td><i class="ri-user-star-line text-warning me-1"></i> <?= htmlspecialchars($car['owner_name']) ?></td>
                                    <td class="fw-bold">$<?= number_format($car['price_per_day'], 2) ?></td>
                                    <td>
                                        <?php if ($car['is_available']): ?>
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3">Live</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Rented</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="delete_car_admin.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-danger rounded-pill px-3" onclick="return confirm('WARNING: This will use a transaction to delete the car AND its booking history. Proceed?')">
                                            <i class="ri-delete-bin-7-line me-1"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No vehicles found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="py-5 bg-dark text-white text-center">
        <div class="container mb-3">
            <a href="#" class="text-white-50 text-decoration-none mx-2 small">Privacy Policy</a>
            <a href="#" class="text-white-50 text-decoration-none mx-2 small">Audit Logs</a>
            <a href="#" class="text-white-50 text-decoration-none mx-2 small">System Health</a>
        </div>
        <p class="mb-0 opacity-50">&copy; 2024 RENTAL Administration Panel.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>