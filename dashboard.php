<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: http://localhost/Proiect/login.php");
    exit();
}

include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Train Booking</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            max-width: 900px;
            width: 100%;
            margin: 60px auto;
            background: white;
            padding: 40px 50px 100px; /* extra bottom padding for buttons */
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
            text-align: center;
        }
        h2 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin: 0 auto 30px;
        }
        table, th, td {
          border: 1px solid #ddd;
        }
        th, td {
          padding: 12px;
          text-align: left;
        }
        th {
          background-color: #4CAF50;
          color: white;
        }
        tr:nth-child(even) {
          background-color: #f2f2f2;
        }

        .button-bar {
            position: absolute;
            bottom: 20px;
            left: 50px;
            right: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .book-ticket-btn,
        .logout-btn {
            padding: 10px 20px;
            font-size: 0.95rem;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s ease;
            font-weight: bold;
        }

        .book-ticket-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
        }

        .book-ticket-btn:hover {
            background-color: #45a049;
        }

        .logout-btn {
            background-color: transparent;
            color: #777;
            border: 1px solid #ccc;
        }

        .logout-btn:hover {
            color: #4CAF50;
            border-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
       <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

<?php
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 1) { // Admin
        echo '<p><a href="admin_trains.php">Manage Trains</a> | <a href="admin_schedules.php">Manage Schedules</a> | <a href="view_users.php">View Users & Tickets</a></p>';
    } elseif ($_SESSION['role'] == 2) { // Worker
        echo '<p><a href="worker_tickets.php">View Tickets</a></p>';
    }
}
?>

<h3>Available Train Rides</h3>
<!-- rest of your code -->


        <?php
        $sql = "SELECT s.id, t.train_name, s.journey_date, 
                       ds.name AS departure_station, 
                       as1.name AS arrival_station,
                       s.departure_time, s.arrival_time
                FROM schedules s
                JOIN trains t ON s.train_id = t.id
                JOIN stations ds ON s.departure_station_id = ds.id
                JOIN stations as1 ON s.arrival_station_id = as1.id";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Train</th>
                        <th>Date</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['train_name']}</td>
                        <td>{$row['journey_date']}</td>
                        <td>{$row['departure_station']}</td>
                        <td>{$row['arrival_station']}</td>
                        <td>{$row['departure_time']}</td>
                        <td>{$row['arrival_time']}</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No train rides available.</p>";
        }
        ?>

        <div class="button-bar">
            <a href="logout.php" class="logout-btn">Logout</a>
            <a href="book_ticket.php" class="book-ticket-btn">Book a Ticket</a>
            <a href="my_bookings.php" class="button">View My Bookings</a>
        </div>
    </div>
</body>
</html>
