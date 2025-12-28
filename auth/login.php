<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RENTAL | Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Remix Icons -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

  <!-- Minimal custom override -->
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f8f9fa;
    }

    .brand {
      font-family: "Syncopate", sans-serif;
      font-weight: 700;
      letter-spacing: -2px;
    }

    .btn-primary {
      background-color: #15191d;
      border: none;
    }

    .btn-primary:hover {
      background-color: #f5b754;
      color: #15191d;
    }

    .login-card {
      border-radius: 1rem;
      overflow: hidden;
    }

    .login-image {
      background: url("../assets/auth-car.png") center/cover no-repeat;
      min-height: 100%;
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

          <!-- Left image -->
          <div class="col-md-6 d-none d-md-block">
            <div class="login-image"></div>
          </div>

          <!-- Login form -->
          <div class="col-md-6 p-5">
            <h2 class="fw-bold mb-2">Welcome Back</h2>
            <p class="text-muted mb-4">Sign in to your account</p>

            <form method="POST" action="login_process.php">

              <div class="mb-3">
                <label class="form-label">
                  <i class="ri-mail-line me-1"></i>Email
                </label>
                <input type="email" name="email" class="form-control" required>
              </div>

              <div class="mb-4">
                <label class="form-label">
                  <i class="ri-lock-line me-1"></i>Password
                </label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="ri-login-box-line me-1"></i> Sign In
              </button>

            </form>

            <div class="text-center mt-4">
              <p class="mb-0">
                Don’t have an account?
                <a href="register.php" class="text-decoration-none fw-semibold">Sign up</a>
              </p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
