<?php
session_start();
require_once "../config/db.php";

// Initialize variables
$loginError = "";
$email = "";
if (isset($_SESSION['user_id']) && $_SESSION['user_id']!= null) {
    header("Location: ../dashboard/customer_dashboard.php");
    exit();
} 

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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                switch ($user['role']) {
                    case 'Customer': header("Location: ../dashboard/customer_dashboard.php"); exit;
                    case 'Owner': header("Location: ../dashboard/owner_dashboard.php"); exit;
                    case 'Admin': header("Location: ../dashboard/admin_dashboard.php"); exit;
                    default: header("Location: login.php?error=invalid"); exit;
                }
            }
        }
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
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
    
    body { font-family: "Poppins", sans-serif; background-color: #f0f2f5; }
    .brand { font-weight: 800; letter-spacing: -1px; color: #15191d !important; }
    .btn-primary { background-color: #15191d; border: none; transition: 0.3s; }
    .btn-primary:hover { background-color: #f5b754; color: #15191d; transform: translateY(-2px); }
    
    .login-card { border: none; border-radius: 1.5rem; overflow: hidden; background: #fff; }
    
    /* Image Side Styling */
    .login-image { 
        /* You can replace this URL with your local ../assets/auth-car.png */
        background: linear-gradient(rgba(21, 25, 29, 0.4), rgba(21, 25, 29, 0.4)), 
                    url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&q=80&w=1000'); 
        background-size: cover; 
        background-position: center; 
        min-height: 100%; 
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 40px;
        color: white;
    }

    .password-container { position: relative; }
    .password-toggle { 
        cursor: pointer; 
        position: absolute; 
        right: 15px; 
        top: 38px; 
        z-index: 10;
        color: #6c757d;
    }

    .form-control { border-radius: 0.8rem; padding: 0.75rem 1rem; border: 1px solid #e0e0e0; }
    .form-control:focus { box-shadow: 0 0 0 0.25 margin-bottom: 5px; rem rgba(245, 183, 84, 0.25); border-color: #f5b754; }
</style>
</head>
<body>

<nav class="navbar navbar-light bg-transparent fixed-top pt-4">
  <div class="container">
    <a class="navbar-brand brand fs-3" href="../index.php">RENTAL<span class="text-warning">.</span></a>
  </div>
</nav>

<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
  <div class="row w-100 justify-content-center">
    <div class="col-lg-10">
      <div class="card login-card shadow-lg">
        <div class="row g-0">
          <div class="col-md-6 d-none d-md-block">
            <div class="login-image">
                <h1 class="fw-bold display-5">Welcome Back.</h1>
                <p class="lead opacity-75">The road is waiting for you. Log in to manage your luxury fleet or book your next journey.</p>
                <div class="mt-4">
                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">Premium Experience</span>
                </div>
            </div>
          </div>
          
          <div class="col-md-6 p-5 bg-white">
            <div class="mb-5">
                <h2 class="fw-bold text-dark">Sign In</h2>
                <p class="text-muted">Enter your credentials to continue</p>
            </div>

            <?php if (!empty($loginError)): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                    <i class="ri-error-warning-line me-2"></i><?= htmlspecialchars($loginError) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
              <div class="mb-4">
                <label class="form-label fw-600">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="ri-mail-line"></i></span>
                    <input type="email" name="email" class="form-control bg-light border-0" placeholder="name@example.com" required>
                </div>
              </div>

              <div class="mb-4 password-container">
                <label class="form-label fw-600">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="ri-lock-line"></i></span>
                    <input type="password" name="password" class="form-control bg-light border-0" id="password" placeholder="••••••••" required>
                    <i class="ri-eye-line password-toggle" id="togglePassword"></i>
                </div>
              </div>

             

              <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary py-3 fw-bold rounded-pill shadow">
                    LOG IN <i class="ri-arrow-right-line ms-2"></i>
                </button>
              </div>

              <div class="text-center">
                <span class="text-muted small">New to Rental?</span>
                <a href="register.php" class="small text-dark fw-bold text-decoration-none ms-1">Create Account</a>
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