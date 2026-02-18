<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 2) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$sql = "
    SELECT 
        tickets.id AS ticket_id,
        tickets.pnr_number,
        tickets.issue_date,
        users.username AS booked_by,
        seats.seat_number,
        seats.class,
        schedules.journey_date,
        t.train_name,
        ds.name AS departure_station,
        as1.name AS arrival_station,
        schedules.departure_time,
        schedules.arrival_time
    FROM tickets
    LEFT JOIN users ON tickets.booking_id = users.id
    LEFT JOIN seats ON tickets.seat_id = seats.id
    LEFT JOIN schedules ON seats.schedule_id = schedules.id
    LEFT JOIN trains t ON schedules.train_id = t.id
    LEFT JOIN stations ds ON schedules.departure_station_id = ds.id
    LEFT JOIN stations as1 ON schedules.arrival_station_id = as1.id
    ORDER BY tickets.issue_date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker - View Tickets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>All Tickets</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>PNR</th>
                <th>Booked By</th>
                <th>Class</th>
                <th>Seat</th>
                <th>Train</th>
                <th>From</th>
                <th>To</th>
                <th>Journey Date</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Issued On</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['pnr_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['booked_by']); ?></td>
                    <td><?php echo htmlspecialchars($row['class']); ?></td>
                    <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['train_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_station']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_station']); ?></td>
                    <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['issue_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No tickets found.</p>
    <?php endif; ?>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>
