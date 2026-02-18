<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$sql = "SELECT 
            u.id AS user_id,
            u.username,
            u.full_name,
            u.email,
            r.role_name,
            b.id AS booking_id,
            b.booking_date,
            b.total_price,
            b.status,
            t.train_name,
            s.journey_date,
            ds.name AS from_station,
            as1.name AS to_station,
            s.departure_time,
            s.arrival_time,
            tk.id AS ticket_id
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN bookings b ON u.id = b.user_id
        LEFT JOIN schedules s ON b.schedule_id = s.id
        LEFT JOIN trains t ON s.train_id = t.id
        LEFT JOIN stations ds ON s.departure_station_id = ds.id
        LEFT JOIN stations as1 ON s.arrival_station_id = as1.id
        LEFT JOIN tickets tk ON b.id = tk.booking_id
        ORDER BY u.id, b.booking_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Users & Bookings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 30px;
            background-color: #f4f4f4;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .back-link {
            margin-top: 20px;
            display: block;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>All Users and Their Bookings</h2>

        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Booking ID</th>
                    <th>Ticket ID</th>
                    <th>Train</th>
                    <th>Journey Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Total Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['user_id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['full_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['role_name']}</td>
                            <td>" . ($row['booking_id'] ?? '-') . "</td>
                            <td>" . ($row['ticket_id'] ?? '-') . "</td>
                            <td>" . ($row['train_name'] ?? '-') . "</td>
                            <td>" . ($row['journey_date'] ?? '-') . "</td>
                            <td>" . ($row['from_station'] ?? '-') . "</td>
                            <td>" . ($row['to_station'] ?? '-') . "</td>
                            <td>" . ($row['departure_time'] ?? '-') . "</td>
                            <td>" . ($row['arrival_time'] ?? '-') . "</td>
                            <td>" . ($row['total_price'] ?? '-') . "</td>
                            <td>" . ($row['status'] ?? '-') . "</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='15'>No users or bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="back-link">← Back to Admin Dashboard</a>
    </div>
</body>
</html>
