<?php
require_once "../config/db.php"; // your DB connection

$uerror = $eerror = $perror = $cperror = ""; // error variables
$message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmpassword"];
    $role = $_POST["role"];

    // Validation
    function validateUser($user) {
        global $uerror;
        if (empty($user)) {
            $uerror = "Username is required.";
        } elseif (!preg_match("/^[a-zA-Z ]+$/", $user)) {
            $uerror = "Username must contain only letters and spaces.";
        } elseif (strlen($user) < 5) {
            $uerror = "Username must be at least 5 characters long.";
        }
    }

    function validateEmail($email) {
        global $eerror;
        if (empty($email)) {
            $eerror = "Email is required.";
        } else {
            $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
                $eerror = "Invalid email format.";
            }
        }
    }

    function validatePassword($password) {
        global $perror;
        if (empty($password)) {
            $perror = "Password is required.";
        } elseif (strlen($password) < 8) {
            $perror = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/\d/', $password)) {
            $perror = "Password must contain at least one number.";
        } elseif (!preg_match('/[\W_]/', $password)) {
            $perror = "Password must contain at least one special character.";
        }
    }

    function validateConfirmPassword($password, $confirmPassword) {
        global $cperror;
        if (empty($confirmPassword)) {
            $cperror = "Confirm Password is required.";
        } elseif ($password !== $confirmPassword) {
            $cperror = "Passwords do not match.";
        }
    }

    validateUser($name);
    validateEmail($email);
    validatePassword($password);
    validateConfirmPassword($password, $confirmPassword);

    if (!$uerror && !$eerror && !$perror && !$cperror) {
        $conn = connect();

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $eerror = "Email already registered.";
        } else {
            // Insert user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            if ($stmt->execute()) {
                header("Location: login.php?success=1");
                exit;
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RENTAL | Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<style>
  body { font-family: "Poppins", sans-serif; background-color: #f8f9fa; }
  .brand { font-family: "Syncopate", sans-serif; font-weight: 700; letter-spacing: -2px; }
  .btn-primary { background-color: #15191d; border: none; }
  .btn-primary:hover { background-color: #f5b754; color: #15191d; }
  .register-card { border-radius: 1rem; overflow: hidden; }
  .register-image { background: url("../assets/auth-car.png") center/cover no-repeat; min-height: 100%; }
</style>
</head>

<body>
<nav class="navbar navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand brand" href="../index.php">RENTAL</a>
  </div>
</nav>

<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
  <div class="row w-100 justify-content-center">
    <div class="col-lg-8">
      <div class="card register-card shadow-lg">
        <div class="row g-0">
          <div class="col-md-6 d-none d-md-block">
            <div class="register-image"></div>
          </div>
          <div class="col-md-6 p-5">
            <h2 class="fw-bold mb-2">Create Account</h2>
            <p class="text-muted mb-4">Join the car rental platform</p>

            <?php if (!empty($message)) echo "<div class='alert alert-danger'>$message</div>"; ?>

            <form method="POST" action="">
              <div class="mb-3">
                <label class="form-label"><i class="ri-user-line me-1"></i>Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required>
                <?php if (!empty($uerror)) echo "<span class='text-danger'>$uerror</span>"; ?>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="ri-mail-line me-1"></i>Email</label>
                <input type="email" name="email" class="form-control" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                <?php if (!empty($eerror)) echo "<span class='text-danger'>$eerror</span>"; ?>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="ri-lock-line me-1"></i>Password</label>
                <input type="password" name="password" class="form-control" required>
                <?php if (!empty($perror)) echo "<span class='text-danger'>$perror</span>"; ?>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="ri-lock-line me-1"></i>Confirm Password</label>
                <input type="password" name="confirmpassword" class="form-control" required>
                <?php if (!empty($cperror)) echo "<span class='text-danger'>$cperror</span>"; ?>
              </div>

              <div class="mb-4">
                <label class="form-label"><i class="ri-shield-user-line me-1"></i>Register As</label>
                <select name="role" class="form-select" required>
                  <option value="">Select role</option>
                  <option value="customer" <?= isset($role) && $role === 'customer' ? 'selected' : '' ?>>Customer</option>
                  <option value="owner" <?= isset($role) && $role === 'owner' ? 'selected' : '' ?>>Car Owner</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2"><i class="ri-user-add-line me-1"></i> Create Account</button>
            </form>

            <div class="text-center mt-4">
              <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-semibold">Login</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
