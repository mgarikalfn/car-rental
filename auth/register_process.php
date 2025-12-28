<?php
$uerror = $eerror = $perror = $cperror = ""; // Validation errors
$message = ""; // Success message

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name            = trim($_POST['user']);
    $email           = trim($_POST['email']);
    $password        = $_POST['password'];
    $confirmPassword = $_POST['confirmpassword'];

    // --------------------
    // Validation functions
    // --------------------
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

    // Run validations
    validateUser($name);
    validateEmail($email);
    validatePassword($password);
    validateConfirmPassword($password, $confirmPassword);

    // If no errors, insert into database
    if (!$uerror && !$eerror && !$perror && !$cperror) {
        require_once 'databaseConnection.php';
        $con = connect();

        // Check if email already exists
        $stmt = mysqli_prepare($con, "SELECT id FROM accounts WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $eerror = "Email already registered.";
        } else {
            // Insert user
            $stmt = mysqli_prepare($con, "INSERT INTO accounts (username, email, password) VALUES (?, ?, ?)");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registration successful! You can now log in.";
                // Clear form fields
                $name = $email = "";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($con);
    }

    if ($result) {
    // Registration successful, redirect to login page
    header("Location: login.php?success=1");
    exit;
} else {
    $message = "Registration failed. Please try again.";
}

}
?>
