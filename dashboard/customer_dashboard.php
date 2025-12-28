<?php
session_start();
require_once "../config/db.php";

// Ensure user is logged in and is a customer
/*  if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit;
} 
 */
$conn = connect();

// Fetch all cars
$sql = "SELECT * FROM cars"; // Assuming table `cars` with fields: id, name, type, image, speed, seats, price, etc.
$result = $conn->query($sql);
$cars = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/styles.css" />
  <title>Customer Dashboard | RENTAL</title>
</head>
<body>

<header>
  <nav>
    <div class="nav__header">
      <div class="nav__logo"><a href="#">RENTAL</a></div>
      <div class="nav__menu__btn" id="menu-btn">
        <i class="ri-menu-line"></i>
      </div>
    </div>
    <ul class="nav__links" id="nav-links">
      <li><a href="#">Home</a></li>
      <li><a href="#cars">Browse Cars</a></li>
      <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="section__container range__container" id="cars">
  <h2 class="section__header">Available Cars</h2>
  <div class="range__grid">
    <?php foreach($cars as $car): ?>
      <div class="range__card">
        <img src="../assets/<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['name']) ?>" />
        <div class="range__details">
          <h4><?= htmlspecialchars($car['name']) ?></h4>
          <p>Type: <?= htmlspecialchars($car['type']) ?></p>
          <p>Speed: <?= htmlspecialchars($car['speed']) ?> km/h</p>
          <p>Seats: <?= htmlspecialchars($car['seats']) ?></p>
          <p>Price: $<?= htmlspecialchars($car['price']) ?>/day</p>
          <a href="book_car.php?id=<?= $car['id'] ?>"><i class="ri-arrow-right-line"></i> Book</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

</body>
</html>
