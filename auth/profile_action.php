<?php
session_start();
require_once "../config/db.php";

// 1. Authentication Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = connect();
$user_id = $_SESSION['user_id'];

// 2. Form Submission Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize Inputs
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Don't trim passwords (spaces can be intentional)

    // 3. Robust Form Validation
    if (empty($name) || empty($email)) {
        header("Location: profile.php?msg=error&details=empty_fields");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: profile.php?msg=error&details=invalid_email");
        exit;
    }

    // 4. Duplicate Email Check (Exclude current user)
    // This ensures no two users end up with the same login ID
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check_email->bind_param("si", $email, $user_id);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        header("Location: profile.php?msg=email_exists");
        exit;
    }

    // 5. Build Dynamic SQL Query
    // We only update the password if the user actually typed a new one
    if (!empty($password)) {
        // Password length validation
        if (strlen($password) < 6) {
            header("Location: profile.php?msg=error&details=password_short");
            exit;
        }

        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $hashed_pw, $user_id);
    } else {
        $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $email, $user_id);
    }

    // 6. Execute and Synchronize Session
    if ($stmt->execute()) {
        // Very important: Update the session so the UI reflects changes instantly
        $_SESSION['user_name'] = $name;
        
        header("Location: profile.php?msg=success");
    } else {
        header("Location: profile.php?msg=error");
    }
    
    $stmt->close();
    $conn->close();
} else {
    // Redirect if page is accessed directly without POST
    header("Location: profile.php");
    exit;
}