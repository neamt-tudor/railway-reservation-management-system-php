<?php
session_start();
include 'db.php';

// Show errors for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $email      = $_POST['email'];
    $full_name  = $_POST['full_name'];
    $role_id    = 3; // default to passenger

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username or email already exists. <a href='register.php'>Try again</a>";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user with role_id = 3 (passenger)
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email, full_name, role_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $username, $hashed_password, $email, $full_name, $role_id);

        if ($stmt->execute()) {
            // 🔧 Here's the modified redirect line:
            header("Location: http://localhost/Proiect/registration_success.php");
            exit();
        } else {
            echo "Error inserting user: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: register.php");
    exit();
}
