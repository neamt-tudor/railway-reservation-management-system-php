<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user data from the database (include role_id!)
    $stmt = $conn->prepare("SELECT id, username, password_hash, role_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // ✅ Set session variables including role
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_id'];

            // ✅ Redirect based on role
            if ($user['role_id'] == 1) {
                header("Location: admin_dashboard.php"); // Admin
            } else {
                header("Location: dashboard.php"); // Normal user
            }
            exit();
        } else {
            echo "Invalid password. <a href='login.php'>Try again</a>";
        }
    } else {
        echo "User not found. <a href='login.php'>Try again</a>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.php");
    exit();
}
