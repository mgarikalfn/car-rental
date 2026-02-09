<?php
session_start();
require_once "../config/db.php"; 

$uerror = $eerror = $perror = $cperror = ""; 
$message = "";
$name = $email = $role = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $conn = connect();
    
    $name = trim($_POST["name"]);
    $email = strtolower(trim($_POST["email"])); // Standardize to lowercase
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmpassword"];
    $role = $_POST["role"];

    // 1. Validations
    if (strlen($name) < 5) { $uerror = "At least 5 characters required."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $eerror = "Invalid email format."; }
    if (strlen($password) < 8) { $perror = "Minimum 8 characters."; }
    if ($password !== $confirmPassword) { $cperror = "Passwords do not match."; }

    if (!$uerror && !$eerror && !$perror && !$cperror) {
        
        // 2. Check if email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $eerror = "This email is already registered.";
            $check->close();
        } else {
            $check->close();
            
            // 3. Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            if ($stmt->execute()) {
                // SUCCESS: Redirect immediately and stop script
                header("Location: login.php?success=registration_complete");
                exit(); 
            } else {
                $message = "Database error: Could not register.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTAL | Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        body { font-family: "Poppins", sans-serif; background-color: #f0f2f5; }
        .brand { font-weight: 800; letter-spacing: -1px; color: #15191d !important; }
        .btn-primary { background-color: #15191d; border: none; transition: 0.3s; padding: 12px; }
        .btn-primary:hover { background-color: #f5b754; color: #15191d; transform: translateY(-2px); }
        
        .register-card { border: none; border-radius: 1.5rem; overflow: hidden; background: #fff; }
        
        .register-image { 
            background: linear-gradient(rgba(21, 25, 29, 0.6), rgba(21, 25, 29, 0.6)), 
                        url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=1000'); 
            background-size: cover; background-position: center; 
            min-height: 100%; display: flex; flex-direction: column;
            justify-content: flex-end; padding: 50px; color: white;
        }

        .form-control, .form-select { 
            border-radius: 0.8rem; padding: 0.7rem 1rem; 
            border: 1px solid #e0e0e0; background-color: #f8f9fa;
        }
        
        .form-control:focus, .form-select:focus { 
            box-shadow: 0 0 0 0.25rem rgba(245, 183, 84, 0.25); 
            border-color: #f5b754; background-color: #fff;
        }
        
        .pass-group { position: relative; }
        .toggle-icon {
            position: absolute; right: 15px; top: 40px;
            cursor: pointer; color: #6c757d; z-index: 10;
        }

        .error-text { font-size: 0.75rem; font-weight: 600; margin-top: 4px; display: block; color: #dc3545; }
    </style>
</head>

<body>
<nav class="navbar navbar-light bg-transparent fixed-top pt-4">
  <div class="container">
    <a class="navbar-brand brand fs-3" href="../index.php">RENTAL<span class="text-warning">.</span></a>
  </div>
</nav>

<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh; padding: 80px 0;">
  <div class="row w-100 justify-content-center">
    <div class="col-lg-11 col-xl-10">
      <div class="card register-card shadow-lg">
        <div class="row g-0">
          
          <div class="col-md-5 d-none d-md-block">
            <div class="register-image">
                <h2 class="fw-bold">Start Your Journey.</h2>
                <p class="opacity-75">Join the most premium car rental community.</p>
            </div>
          </div>
          
          <div class="col-md-7 p-4 p-lg-5">
            <h2 class="fw-bold text-dark mb-1">Create Account</h2>
            <p class="text-muted mb-4">Join RENTAL today</p>

            <?php if (!empty($message)) echo "<div class='alert alert-danger border-0 small py-2'>$message</div>"; ?>

            <form method="POST" action="register.php">
              <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-uppercase">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" value="<?= htmlspecialchars($name) ?>" required>
                    <?php if (!empty($uerror)) echo "<span class='error-text'>$uerror</span>"; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-uppercase">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="john@example.com" value="<?= htmlspecialchars($email) ?>" required>
                    <?php if (!empty($eerror)) echo "<span class='error-text'>$eerror</span>"; ?>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3 pass-group">
                    <label class="form-label small fw-bold text-uppercase">Password</label>
                    <input type="password" name="password" id="regPassword" class="form-control" placeholder="••••••••" required>
                    <i class="ri-eye-line toggle-icon" onclick="togglePass('regPassword', this)"></i>
                    <?php if (!empty($perror)) echo "<span class='error-text'>$perror</span>"; ?>
                </div>

                <div class="col-md-6 mb-3 pass-group">
                    <label class="form-label small fw-bold text-uppercase">Confirm</label>
                    <input type="password" name="confirmpassword" id="confirmPassword" class="form-control" placeholder="••••••••" required>
                    <i class="ri-eye-line toggle-icon" onclick="togglePass('confirmPassword', this)"></i>
                    <?php if (!empty($cperror)) echo "<span class='error-text'>$cperror</span>"; ?>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label small fw-bold text-uppercase">Register As</label>
                <select name="role" class="form-select" required>
                  <option value="Customer" <?= ($role == 'Customer') ? 'selected' : '' ?>>Customer (I want to rent)</option>
                  <option value="Owner" <?= ($role == 'Owner') ? 'selected' : '' ?>>Car Owner (I want to list)</option>
                </select>
              </div>

              <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary fw-bold rounded-pill shadow">
                    CREATE ACCOUNT <i class="ri-user-add-line ms-2"></i>
                </button>
              </div>

              <div class="text-center">
                <span class="text-muted small">Already have an account?</span>
                <a href="login.php" class="small text-dark fw-bold text-decoration-none ms-1">Login here</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function togglePass(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
    } else {
        input.type = "password";
        icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
    }
}
</script>
</body>
</html>