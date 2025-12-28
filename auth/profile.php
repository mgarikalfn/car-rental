<?php
session_start();
require_once "../config/db.php";

// Auth Guard - Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();
$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Determine back link based on role
$back_link = ($user['role'] === 'Owner') ? '../dashboard/owner_dashboard.php' : '../dashboard/customer_dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --primary-gold: #f5b754;
            --dark-slate: #1a1d21;
        }
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        
        .profile-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.04);
            background: white;
        }

        .avatar-section {
            width: 100px;
            height: 100px;
            background: var(--dark-slate);
            color: var(--primary-gold);
            font-size: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: -50px auto 20px;
            border: 5px solid #f8f9fa;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-gold);
        }

        .input-group-text {
            border: none;
            background-color: #f1f3f5;
        }

        .btn-save {
            background-color: var(--dark-slate);
            color: white;
            transition: 0.3s;
        }

        .btn-save:hover {
            background-color: #000;
            color: var(--primary-gold);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="#">RENTAL <span class="text-warning">HUB</span></a>
        <a href="<?= $back_link ?>" class="btn btn-outline-dark btn-sm rounded-pill px-4">
            <i class="ri-arrow-left-s-line"></i> Dashboard
        </a>
    </div>
</nav>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-dismissible fade show rounded-4 shadow-sm mb-4 <?php echo ($_GET['msg'] == 'success') ? 'bg-success text-white' : 'bg-danger text-white'; ?>" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="<?php echo ($_GET['msg'] == 'success') ? 'ri-checkbox-circle-line' : 'ri-error-warning-line'; ?> me-2 fs-5"></i>
                        <span>
                            <?php 
                                if($_GET['msg'] == 'success') echo "Account updated successfully!";
                                if($_GET['msg'] == 'email_exists') echo "Error: Email is already associated with another account.";
                                if($_GET['msg'] == 'error') echo "An error occurred while saving.";
                            ?>
                        </span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card profile-card">
                <div class="card-body p-5">
                    <div class="avatar-section fw-bold">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>

                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-0"><?= htmlspecialchars($user['name']) ?></h3>
                        <span class="badge bg-light text-muted rounded-pill border px-3"><?= strtoupper($user['role']) ?></span>
                    </div>

                    <form action="profile_action.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">This is used as your unique login ID.</div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Change Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-lock-password-line"></i></span>
                                <input type="password" name="password" id="passwordField" class="form-control bg-light border-0 py-2" placeholder="Leave blank to keep current">
                                <button class="btn btn-light border-0" type="button" id="togglePassword">
                                    <i class="ri-eye-line" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-save w-100 py-3 rounded-pill fw-bold">
                            SAVE ACCOUNT SETTINGS
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small">Member since <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Password Visibility Toggle Logic
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#passwordField');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the eye icon style
        if (type === 'password') {
            eyeIcon.classList.add('ri-eye-line');
            eyeIcon.classList.remove('ri-eye-off-line');
        } else {
            eyeIcon.classList.remove('ri-eye-line');
            eyeIcon.classList.add('ri-eye-off-line');
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>