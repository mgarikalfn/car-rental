<?php
session_start();
require_once "../config/db.php";

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user info
$conn = connect();
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$name = htmlspecialchars($user['name']);
$role = $user['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
<link rel="stylesheet" href="../css/styles.css"/>
<title>RENTAL | Dashboard</title>
</head>
<body>

<header>
  <nav>
    <div class="nav__header">
      <div class="nav__logo">
        <a href="#">RENTAL</a>
      </div>
      <div class="nav__menu__btn" id="menu-btn">
        <i class="ri-menu-line"></i>
      </div>
    </div>
    <ul class="nav__links" id="nav-links">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#about">About</a></li>
      <li><a href="#contact">Contact</a></li>
    </ul>
    <div class="nav__btn">
      <span class="me-3">Hello, <?= $name ?></span>
      <a href="logout.php" class="btn">Logout</a>
    </div>
  </nav>
</header>

<section class="section__container" id="features">
  <h2 class="section__header text-center">DASHBOARD</h2>
  <div class="range__grid">
    <?php if($role === 'customer'): ?>
      <div class="range__card">
        <h4>Browse Cars</h4>
        <a href="browse_cars.php"><i class="ri-arrow-right-line"></i></a>
      </div>
      <div class="range__card">
        <h4>My Bookings</h4>
        <a href="my_bookings.php"><i class="ri-arrow-right-line"></i></a>
      </div>
    <?php elseif($role === 'owner'): ?>
      <div class="range__card">
        <h4>Manage Cars</h4>
        <a href="manage_cars.php"><i class="ri-arrow-right-line"></i></a>
      </div>
      <div class="range__card">
        <h4>Bookings Received</h4>
        <a href="owner_bookings.php"><i class="ri-arrow-right-line"></i></a>
      </div>
    <?php elseif($role === 'admin'): ?>
      <div class="range__card">
        <h4>Manage Users</h4>
        <a href="all_users.php"><i class="ri-arrow-right-line"></i></a>
      </div>
      <div class="range__card">
        <h4>Manage Bookings</h4>
        <a href="all_bookings.php"><i class="ri-arrow-right-line"></i></a>
      </div>
    <?php endif; ?>
  </div>
</section>

<footer>
  <div class="section__container footer__container">
    <div class="footer__col">
      <h4>Resources</h4>
      <ul class="footer__links">
        <li><a href="#">Installation Manual</a></li>
        <li><a href="#">Release Note</a></li>
        <li><a href="#">Community Help</a></li>
      </ul>
    </div>
    <div class="footer__col">
      <h4>Company</h4>
      <ul class="footer__links">
        <li><a href="#">About Us</a></li>
        <li><a href="#">Career</a></li>
        <li><a href="#">Press</a></li>
        <li><a href="#">Support</a></li>
      </ul>
    </div>
    <div class="footer__col">
      <h4>Product</h4>
      <ul class="footer__links">
        <li><a href="#">Demo</a></li>
        <li><a href="#">Security</a></li>
        <li><a href="#">FAQ</a></li>
        <li><a href="#">Features</a></li>
      </ul>
    </div>
    <div class="footer__col">
      <h4>Follow Us</h4>
      <ul class="footer__socials">
        <li><a href="#"><i class="ri-facebook-fill"></i></a></li>
        <li><a href="#"><i class="ri-twitter-fill"></i></a></li>
        <li><a href="#"><i class="ri-linkedin-fill"></i></a></li>
      </ul>
    </div>
  </div>
  <div class="footer__bar">
    Copyright © 2024 RENTAL. All rights reserved.
  </div>
</footer>

<script src="https://unpkg.com/scrollreveal"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
