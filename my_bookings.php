<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

$sql = "SELECT t.pnr_number, t.issue_date, s.journey_date, s.departure_time, s.arrival_time,
               ds.name AS from_station, as1.name AS to_station,
               tr.train_name, se.seat_number, se.class, b.status, b.total_price
        FROM tickets t
        JOIN bookings b ON t.booking_id = b.id
        JOIN schedules s ON b.schedule_id = s.id
        JOIN trains tr ON s.train_id = tr.id
        JOIN stations ds ON s.departure_station_id = ds.id
        JOIN stations as1 ON s.arrival_station_id = as1.id
        JOIN seats se ON t.seat_id = se.id
        WHERE b.user_id = ?
        ORDER BY s.journey_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>My Bookings</h2>
    <a href="dashboard.php" style="display:inline-block; margin-bottom:20px;">&larr; Back to Dashboard</a>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>PNR</th>
                <th>Train</th>
                <th>From</th>
                <th>To</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Journey Date</th>
                <th>Seat</th>
                <th>Class</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['pnr_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['train_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['from_station']); ?></td>
                    <td><?php echo htmlspecialchars($row['to_station']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['total_price']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
</div>
</body>
</html>
