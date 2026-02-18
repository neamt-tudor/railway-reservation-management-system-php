<?php
include 'db.php';

$users = [
    ['username' => 'TudorN', 'password' => 'parola1'],
    ['username' => 'MelaniaB', 'password' => 'parola2'],
    ['username' => 'LucianN', 'password' => 'parola3']
];

foreach ($users as $user) {
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    $stmt->bind_param("ss", $hash, $user['username']);
    $stmt->execute();
    echo "Updated password for {$user['username']}<br>";
}

$conn->close();
?>
