<?php
session_start();
// If user is already logged in, we can change the button to 'Dashboard'
$is_logged_in = isset($_SESSION['user_id']);
$user_dashboard = ($is_logged_in && $_SESSION['user_role'] == 'Owner') ? 'owner/owner_dashboard.php' : 'customer/customer_dashboard.php';
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
                        <li class="nav-item"><a class="nav-link btn btn-warning text-dark px-4 rounded-pill fw-bold" href="<?= $user_dashboard ?>">My Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn btn-warning text-dark px-4 rounded-pill fw-bold" href="auth/login.php">Sign In</a></li>
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
                    <p class="lead mb-5 opacity-75">Unlock the road with our exclusive fleet of luxury and performance vehicles. Book in seconds, drive for a lifetime.</p>

                    <form action="auth/login.php" method="GET" class="search-box p-2 bg-white rounded-pill shadow-lg d-flex align-items-center">
                        <div class="flex-grow-1 px-4">
                            <label class="small text-muted d-block fw-bold text-uppercase" style="letter-spacing: 1px;">Find your dream car</label>
                            <div class="d-flex align-items-center">
                                <i class="ri-car-fill text-warning me-2 fs-5"></i>
                                <input type="text" name="query" class="form-control border-0 p-0 fw-bold fs-5" placeholder="Search e.g. 'Porsche', 'SUV', 'BMW'..." required>
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
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
                        <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&q=80&w=800" class="card-img-top" alt="Porsche">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Porsche 911 Carrera</h5>
                            <div class="d-flex justify-content-between text-muted small mb-3">
                                <span><i class="ri-settings-line"></i> Automatic</span>
                                <span><i class="ri-user-line"></i> 2 Seats</span>
                                <span><i class="ri-gas-station-line"></i> Petrol</span>
                            </div>
                            <a href="auth/login.php" class="btn btn-outline-dark w-100 rounded-pill">Rent Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
                        <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&q=80&w=800" class="card-img-top" alt="BMW">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">BMW M4 Competition</h5>
                            <div class="d-flex justify-content-between text-muted small mb-3">
                                <span><i class="ri-settings-line"></i> Automatic</span>
                                <span><i class="ri-user-line"></i> 4 Seats</span>
                                <span><i class="ri-gas-station-line"></i> Petrol</span>
                            </div>
                            <a href="auth/login.php" class="btn btn-outline-dark w-100 rounded-pill">Rent Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
                        <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&q=80&w=800" class="card-img-top" alt="SUV">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Range Rover Sport</h5>
                            <div class="d-flex justify-content-between text-muted small mb-3">
                                <span><i class="ri-settings-line"></i> Automatic</span>
                                <span><i class="ri-user-line"></i> 5 Seats</span>
                                <span><i class="ri-gas-station-line"></i> Diesel</span>
                            </div>
                            <a href="auth/login.php" class="btn btn-outline-dark w-100 rounded-pill">Rent Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="about">
        <div class="container py-5">
            <div class="row g-4 text-center">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-4 rounded-4 bg-white border h-100 transition-up">
                        <div class="icon-circle mb-3 mx-auto bg-warning-subtle text-warning">
                            <i class="ri-shield-check-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Fully Insured</h4>
                        <p class="text-muted">Every rental comes with comprehensive insurance coverage for your peace of mind.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-4 rounded-4 bg-white border h-100 transition-up">
                        <div class="icon-circle mb-3 mx-auto bg-warning-subtle text-warning">
                            <i class="ri-flashlight-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Instant Booking</h4>
                        <p class="text-muted">No paperwork, no waiting. Choose your car and get the keys in under 5 minutes.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-4 rounded-4 bg-white border h-100 transition-up">
                        <div class="icon-circle mb-3 mx-auto bg-warning-subtle text-warning">
                            <i class="ri-map-pin-2-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold">Anywhere Delivery</h4>
                        <p class="text-muted">We deliver the car to your doorstep, airport, or hotel at no extra cost.</p>
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
                    <p class="text-muted">Elevating your journey with premium car rentals worldwide. Experience luxury redefined.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-4"><i class="ri-instagram-line"></i></a>
                        <a href="#" class="text-white fs-4"><i class="ri-twitter-x-line"></i></a>
                        <a href="#" class="text-white fs-4"><i class="ri-facebook-circle-line"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 offset-lg-2">
                    <h6 class="fw-bold mb-4">Quick Links</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><a href="#fleet" class="text-decoration-none text-muted">Fleet</a></li>
                        <li class="mb-2"><a href="#about" class="text-decoration-none text-muted">Services</a></li>
                        <li class="mb-2"><a href="auth/login.php" class="text-decoration-none text-muted">Login</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-4">Newsletter</h6>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control bg-transparent border-secondary text-white" placeholder="Email address">
                        <button class="btn btn-warning" type="button">Join</button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

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