<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-panel {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            background-color: #f4f4f4;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .admin-panel h2 {
            text-align: center;
        }

        .admin-panel ul {
            list-style: none;
            padding: 0;
        }

        .admin-panel li {
            margin: 15px 0;
        }

        .admin-panel a {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            display: inline-block;
            border-radius: 5px;
        }

        .admin-panel a:hover {
            background-color: #0056b3;
        }

        .logout {
            text-align: center;
            margin-top: 30px;
        }

        .logout a {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="admin-panel">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="admin_trains.php">Manage Trains</a></li>
            <li><a href="admin_schedules.php">Manage Schedules</a></li>
            <li><a href="view_users.php">View Users & Bookings</a></li>
        </ul>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
