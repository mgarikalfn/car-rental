<?php
session_start();
require_once "../config/db.php";

$owner_id = $_SESSION['user_id'] ?? null;
/* if (!$owner_id || $_SESSION['user_role'] !== 'Owner') {
    header("Location: ../auth/login.php");
    exit;
}
 */
$conn = connect();
$car_id = (int)$_GET['id'];
$errors = [];
$success = "";

// 1. Fetch Current Data
$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $car_id, $owner_id);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();

if (!$car) {
    die("Unauthorized access or car not found.");
}

// 2. Handle Update Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['car_name']);
    $cat = $_POST['category'];
    $price = $_POST['price_per_day'];
    $desc = trim($_POST['description']);
    $avail = isset($_POST['is_available']) ? 1 : 0;
    $seats = (int)$_POST['seats'];
    $imageName = $car['image']; // Default to old image

    // Validation
    if (empty($name)) $errors['car_name'] = "Name is required.";

    // Image Update Logic
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $newImage = uniqid("car_", true) . "." . $ext;

        if (move_uploaded_file($_FILES['image']['tmp_name'], "../assets/" . $newImage)) {
            // Delete old image file
            if (file_exists("../assets/" . $car['image'])) unlink("../assets/" . $car['image']);
            $imageName = $newImage;
        }
    }

    if (empty($errors)) {
        // Added seats=? to the UPDATE query and "i" to bind_param
        $upd = $conn->prepare("UPDATE cars SET car_name=?, category=?, seats=?, price_per_day=?, description=?, image=?, is_available=? WHERE id=? AND owner_id=?");
        $upd->bind_param("sssdssiii", $name, $cat, $seats, $price, $desc, $imageName, $avail, $car_id, $owner_id);
        if ($upd->execute()) {
            $success = "Vehicle updated successfully!";
            // Refresh local data
            $car['car_name'] = $name;
            $car['category'] = $cat;
            $car['price_per_day'] = $price;
            $car['description'] = $desc;
            $car['is_available'] = $avail;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Vehicle | RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .edit-container {
            max-width: 700px;
            margin: 50px auto;
        }

        .current-img {
            width: 150px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container edit-container">
        <div class="range__card text-start p-4 p-lg-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Edit Vehicle</h3>
                <a href="owner_dashboard.php" class="btn btn-outline-dark btn-sm">Back to Dashboard</a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold small">CAR NAME</label>
                    <input type="text" name="car_name" class="form-control" value="<?= htmlspecialchars($car['car_name']) ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">CATEGORY</label>
                        <select name="category" class="form-select">
                            <option <?= $car['category'] == 'Luxury' ? 'selected' : '' ?>>Luxury</option>
                            <option <?= $car['category'] == 'SUV' ? 'selected' : '' ?>>SUV</option>
                            <option <?= $car['category'] == 'Sports' ? 'selected' : '' ?>>Sports</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">SEATS</label>
                        <input type="number" name="seats" class="form-control" value="<?= $car['seats'] ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small">PRICE/DAY ($)</label>
                        <input type="number" name="price_per_day" class="form-control" value="<?= $car['price_per_day'] ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">DESCRIPTION</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($car['description']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small d-block">CURRENT IMAGE</label>
                    <img src="../assets/<?= $car['image'] ?>" class="current-img border" alt="car">
                    <input type="file" name="image" class="form-control mt-2">
                    <small class="text-muted">Leave blank to keep current image</small>
                </div>

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_available" id="avail" <?= $car['is_available'] ? 'checked' : '' ?>>
                    <label class="form-check-label fw-bold small" for="avail">Available for Rent</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3">SAVE CHANGES</button>
            </form>
        </div>
    </div>

</body>

</html>