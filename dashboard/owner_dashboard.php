<?php include 'owner_logic.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard | RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">

    <style>
        /* Dashboard Specific Styles */
        .nav-tabs {
            border: none;
            gap: 10px;
        }

        .nav-tabs .nav-link {
            color: #15191d;
            border: 1px solid #15191d;
            border-radius: 5px;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-tabs .nav-link.active {
            background-color: #f5b754 !important;
            color: #15191d !important;
            border-color: #f5b754;
        }

        .car__img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            font-size: 0.85rem;
            color: #dc3545;
            display: block;
            margin-top: 5px;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>

    <header>
        <nav>
            <div class="nav__header">
                <div class="nav__logo"><a href="#">RENTAL</a></div>
            </div>
            <ul class="nav__links d-none d-md-flex">
                <li><a href="#view_fleet">Fleet</a></li>
                <li><a href="#view_bookings">Bookings</a></li>
            </ul>
            <div class="nav__btn">
                <span class="me-3 small text-muted">Owner: <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
                <a href="../auth/logout.php" class="btn btn-primary">Logout</a>
            </div>
        </nav>
    </header>

    <main class="section__container">
        <h2 class="section__header">OWNER CONTROL PANEL</h2>

        <ul class="nav nav-tabs justify-content-center mb-5" id="ownerTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link <?= !isset($_POST['add_car']) ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#view_fleet">My Fleet</button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?= isset($_POST['add_car']) ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#add_vehicle">Add New Vehicle</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#view_bookings">Recent Bookings</button>
            </li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane fade <?= !isset($_POST['add_car']) ? 'show active' : '' ?>" id="view_fleet">
                <div class="range__grid">
                    <?php if ($cars_result->num_rows > 0): ?>
                        <?php while ($car = $cars_result->fetch_assoc()): ?>
                            <div class="range__card">
                                <img src="../assets/<?= $car['image'] ?>" alt="car" class="car__img mb-3">
                                <div class="text-start px-2">
                                    <h4 class="mb-1"><?= htmlspecialchars($car['car_name']) ?></h4>
                                    <p class="text-muted small mb-3"><?= $car['category'] ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">$<?= number_format($car['price_per_day'], 2) ?>/day</span>
                                        <div>
                                            <a href="edit_car.php?id=<?= $car['id'] ?>" class="me-2 text-dark"><i class="ri-edit-box-line"></i></a>
                                            <a href="delete_car.php?id=<?= $car['id'] ?>" class="text-danger" onclick="return confirm('Remove this vehicle?')"><i class="ri-delete-bin-line"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center col-12 py-5">
                            <p class="text-muted">You haven't added any cars yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-pane fade <?= isset($_POST['add_car']) ? 'show active' : '' ?>" id="add_vehicle">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="range__card p-4 p-lg-5 text-start">
                            <h4 class="text-center mb-4">Register New Vehicle</h4>

                            <?php if ($success): ?>
                                <div class="alert alert-success border-0 shadow-sm mb-4">
                                    <i class="ri-checkbox-circle-line me-2"></i><?= $success ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($errors['db'])): ?>
                                <div class="alert alert-danger border-0 shadow-sm mb-4 small"><?= $errors['db'] ?></div>
                            <?php endif; ?>

                            <form action="owner_dashboard.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="add_car">

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">CAR MODEL NAME</label>
                                    <input type="text" name="car_name" class="form-control <?= isset($errors['car_name']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($car_name) ?>" placeholder="e.g. BMW M4 Competition">
                                    <?php if (isset($errors['car_name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['car_name'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">CATEGORY</label>
                                        <select name="category" class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>">
                                            <option value="">Select Type</option>
                                            <option value="Luxury" <?= $category == 'Luxury' ? 'selected' : '' ?>>Luxury</option>
                                            <option value="SUV" <?= $category == 'SUV' ? 'selected' : '' ?>>SUV</option>
                                            <option value="Sports" <?= $category == 'Sports' ? 'selected' : '' ?>>Sports</option>
                                            <option value="Van" <?= $category == 'Van' ? 'selected' : '' ?>>Van</option>
                                        </select>
                                        <?php if (isset($errors['category'])): ?>
                                            <div class="invalid-feedback"><?= $errors['category'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small fw-bold">SEATS</label>
                                        <input type="number" name="seats" class="form-control <?= isset($errors['seats']) ? 'is-invalid' : '' ?>"
                                            value="<?= htmlspecialchars($seats) ?>" placeholder="e.g. 5">
                                        <?php if (isset($errors['seats'])): ?>
                                            <div class="invalid-feedback"><?= $errors['seats'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">PRICE PER DAY ($)</label>
                                        <input type="number" name="price_per_day" class="form-control <?= isset($errors['price_per_day']) ? 'is-invalid' : '' ?>"
                                            value="<?= htmlspecialchars($price_per_day) ?>" placeholder="0.00">
                                        <?php if (isset($errors['price_per_day'])): ?>
                                            <div class="invalid-feedback"><?= $errors['price_per_day'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">DESCRIPTION</label>
                                    <textarea name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                        rows="3" placeholder="Tell customers about the features..."><?= htmlspecialchars($description) ?></textarea>
                                    <?php if (isset($errors['description'])): ?>
                                        <div class="invalid-feedback"><?= $errors['description'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold">VEHICLE IMAGE</label>
                                    <input type="file" name="image" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>">
                                    <?php if (isset($errors['image'])): ?>
                                        <div class="invalid-feedback"><?= $errors['image'] ?></div>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 mt-2 shadow-sm">
                                    ADD TO FLEET <i class="ri-add-circle-line ms-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="view_bookings">
                <div class="table-container">
                    <h4 class="mb-4">Recent Rental Requests</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Vehicle</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($bookings_result->num_rows > 0): ?>
                                    <?php while ($b = $bookings_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($b['customer_name']) ?></strong></td>
                                            <td><?= htmlspecialchars($b['car_name']) ?></td>
                                            <td><small><?= $b['start_date'] ?> to <?= $b['end_date'] ?></small></td>
                                            <td><span class="badge rounded-pill bg-warning text-dark px-3"><?= ucfirst($b['status']) ?></span></td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="update_booking.php?id=<?= $b['id'] ?>&action=approve" class="btn btn-sm btn-outline-success">Approve</a>
                                                    <a href="update_booking.php?id=<?= $b['id'] ?>&action=reject" class="btn btn-sm btn-outline-danger">Reject</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No bookings found yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer>
        <div class="footer__bar">
            Copyright © 2024 RENTAL. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>