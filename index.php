<?php
session_start();
// Check if user is logged in to change navigation behavior
$is_logged_in = isset($_SESSION['user_id']);

// Determine the correct dashboard link based on role
$user_dashboard = 'auth/login.php'; // Default fallback
if ($is_logged_in) {
    $user_dashboard = ($_SESSION['user_role'] == 'Owner') ? 'dashboard/owner_dashboard.php' : 'dashboard/customer_dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTAL | Premium Car Subscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/landing_styles.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3" id="mainNav">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="#">RENTAL<span class="text-warning">.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto gap-4 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#fleet">Fleet</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-warning text-dark px-4 rounded-pill fw-bold shadow-sm" href="<?= $user_dashboard ?>">
                                <i class="ri-dashboard-line me-1"></i> My Dashboard
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Sign In</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-warning text-dark px-4 rounded-pill fw-bold shadow-sm" href="auth/register.php">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section d-flex align-items-center">
        <div class="container text-white">
            <div class="row">
                <div class="col-lg-7" data-aos="fade-right" data-aos-duration="1200">
                    <span class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill fw-bold">PREMIUM RENTALS</span>
                    <h1 class="display-2 fw-bold mb-4">Drive the Experience, <br><span class="text-warning">Not Just a Car.</span></h1>
                    <p class="lead mb-5 opacity-75">Unlock the road with our exclusive fleet of luxury vehicles. Book in seconds, drive for a lifetime.</p>

                    <form action="<?= $is_logged_in ? $user_dashboard : 'auth/login.php' ?>" method="GET" class="search-box p-2 bg-white rounded-pill shadow-lg d-flex align-items-center">
                        <div class="flex-grow-1 px-4 text-dark">
                            <label class="small text-muted d-block fw-bold text-uppercase" style="letter-spacing: 1px;">Find your dream car</label>
                            <div class="d-flex align-items-center">
                                <i class="ri-car-fill text-warning me-2 fs-5"></i>
                                <input type="text" name="query" class="form-control border-0 p-0 fw-bold fs-5 bg-transparent" placeholder="Search Porsche, BMW, SUV..." required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark rounded-pill px-5 py-3 ms-2 fw-bold text-uppercase shadow-sm">
                            <i class="ri-search-line me-1"></i> Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light" id="fleet">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold display-5">Our Elite <span class="text-warning">Fleet</span></h2>
                <p class="text-muted">Hand-picked vehicles for your comfort and style</p>
            </div>
            <div class="row g-4">
                <?php
                // Pre-defined fleet for demonstration
                $fleet = [
                    ['name' => 'Porsche 911 Carrera', 'img' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&q=80&w=800', 'seats' => '2 Seats', 'fuel' => 'Petrol'],
                    ['name' => 'BMW M4 Competition', 'img' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&q=80&w=800', 'seats' => '4 Seats', 'fuel' => 'Petrol'],
                    ['name' => 'Range Rover Sport', 'img' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&q=80&w=800', 'seats' => '5 Seats', 'fuel' => 'Diesel']
                ];
                
                foreach ($fleet as $index => $car):
                ?>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="<?= ($index + 1) * 100 ?>">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100">
                        <img src="<?= $car['img'] ?>" class="card-img-top" alt="<?= $car['name'] ?>" style="height: 220px; object-fit: cover;">
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold mb-3"><?= $car['name'] ?></h5>
                            <div class="d-flex justify-content-between text-muted small mb-4">
                                <span><i class="ri-settings-line"></i> Automatic</span>
                                <span><i class="ri-user-line"></i> <?= $car['seats'] ?></span>
                                <span><i class="ri-gas-station-line"></i> <?= $car['fuel'] ?></span>
                            </div>
                            
                            <div class="mt-auto">
                                <?php if ($is_logged_in): ?>
                                    <a href="<?= $user_dashboard ?>" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Rent Now</a>
                                <?php else: ?>
                                    <div class="d-flex gap-2">
                                        <a href="auth/login.php" class="btn btn-outline-dark flex-grow-1 rounded-pill py-2 small fw-bold">Sign In</a>
                                        <a href="auth/register.php" class="btn btn-warning flex-grow-1 rounded-pill py-2 small fw-bold">Join Now</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5" id="about">
        <div class="container py-5 text-center">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-4 rounded-4 bg-white border h-100">
                        <div class="icon-circle mb-3 mx-auto bg-warning-subtle text-warning" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="ri-shield-check-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Fully Insured</h4>
                        <p class="text-muted small">Comprehensive insurance coverage for total peace of mind on every journey.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-4 rounded-4 bg-white border h-100">
                        <div class="icon-circle mb-3 mx-auto bg-warning-subtle text-warning" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="ri-flashlight-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Instant Booking</h4>
                        <p class="text-muted small">Forget paperwork. Select your car and hit the road in minutes.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-4 rounded-4 bg-white border h-100">
                        <div class="icon-circle mb-3 mx-auto bg-warning-subtle text-warning" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="ri-map-pin-2-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Anywhere Delivery</h4>
                        <p class="text-muted small">Doorstep, airport, or hotel delivery service available at your request.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="fw-bold mb-4">RENTAL<span class="text-warning">.</span></h3>
                    <p class="text-muted small">Elevating your journey with premium car rentals. Experience luxury redefined with our seamless rental platform.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-4"><i class="ri-instagram-line"></i></a>
                        <a href="#" class="text-white fs-4"><i class="ri-twitter-x-line"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 offset-lg-2">
                    <h6 class="fw-bold mb-4 small text-uppercase" style="letter-spacing: 1px;">Company</h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2"><a href="#fleet" class="text-decoration-none text-muted">Fleet</a></li>
                        <li class="mb-2"><a href="#about" class="text-decoration-none text-muted">Services</a></li>
                        <li class="mb-2"><a href="auth/register.php" class="text-decoration-none text-muted">Join Community</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-4 small text-uppercase" style="letter-spacing: 1px;">Newsletter</h6>
                    <p class="text-muted small mb-3">Get the latest offers and fleet updates.</p>
                    <div class="input-group">
                        <input type="email" class="form-control bg-transparent border-secondary text-white small" placeholder="Email address">
                        <button class="btn btn-warning fw-bold px-3" type="button">Join</button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-dark', 'shadow', 'py-2');
                nav.classList.remove('py-3');
            } else {
                nav.classList.remove('bg-dark', 'shadow', 'py-2');
                nav.classList.add('py-3');
            }
        });
    </script>
</body>
</html>