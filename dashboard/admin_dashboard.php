<?php
session_start();
/* if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
} */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | RENTAL</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
<header>
    <nav>
        <div class="nav__header">
            <div class="nav__logo"><a href="#">RENTAL</a></div>
        </div>
        <div class="nav__btn">
            <a href="../auth/logout.php" class="btn">Logout</a>
        </div>
    </nav>
</header>

<main>
    <section class="section__container">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        <p>Admin Panel: manage users, cars, and system settings.</p>
        <!-- Add admin functionalities here -->
    </section>
</main>
</body>
</html>
