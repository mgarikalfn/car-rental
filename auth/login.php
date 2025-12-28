<?php
session_start();
require_once "../config/db.php";

// Initialize variables
$loginError = "";
$email = "";

// 1. Check if an error was passed via the URL (GET)
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'invalid') {
        $loginError = "Invalid email or password.";
    } elseif ($_GET['error'] == 'required') {
        $loginError = "Both email and password are required.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        // Redirect to clear POST data
        header("Location: login.php?error=required");
        exit;
    } else {
        $conn = connect();
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Store user credentials in Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                header("Location: ../" . strtolower($user['role']) . "/dashboard.php");
                exit;
            }
        }
        
        // If we reach here, login failed. Redirect to clear POST data.
        $stmt->close();
        $conn->close();
        header("Location: login.php?error=invalid");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RENTAL | Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
<style>
body { font-family: "Poppins", sans-serif; background-color: #f8f9fa; }
.brand { font-family: "Syncopate", sans-serif; font-weight: 700; letter-spacing: -2px; }
.btn-primary { background-color: #15191d; border: none; }
.btn-primary:hover { background-color: #f5b754; color: #15191d; }
.login-card { border-radius: 1rem; overflow: hidden; }
.login-image { background: url("../assets/auth-car.png") center/cover no-repeat; min-height: 100%; }

/* Fixed Password Toggle Positioning */
.password-container { 
    position: relative; 
}
.password-toggle { 
    cursor: pointer; 
    position: absolute; 
    right: 12px; 
    /* Centers icon vertically inside the input, adjusting for the label height */
    top: 38px; 
    z-index: 10;
    color: #6c757d;
    font-size: 1.2rem;
}
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
      <div class="card login-card shadow-lg">
        <div class="row g-0">
          <div class="col-md-6 d-none d-md-block">
            <div class="login-image"></div>
          </div>
          <div class="col-md-6 p-5">
            <h2 class="fw-bold mb-2">Login</h2>
            <p class="text-muted mb-4">Access your car rental account</p>

            <?php if (!empty($loginError)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($loginError) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
              <div class="mb-3">
                <label class="form-label"><i class="ri-mail-line me-1"></i>Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>

              <div class="mb-3 password-container">
                <label class="form-label"><i class="ri-lock-line me-1"></i>Password</label>
                <input type="password" name="password" class="form-control" id="password" required>
                <i class="ri-eye-line password-toggle" id="togglePassword"></i>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary w-100 py-2"><i class="ri-login-box-line me-1"></i> Login</button>
              </div>

              <div class="text-center mt-3">
                <span>Don't have an account?</span><br>
                <a href="register.php">Register</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const togglePassword = document.querySelector('#togglePassword');
const passwordInput = document.querySelector('#password');

togglePassword.addEventListener('click', () => {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    togglePassword.classList.toggle('ri-eye-line');
    togglePassword.classList.toggle('ri-eye-off-line');
});
</script>
</body>
</html>