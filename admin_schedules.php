<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$message = "";

// Fetch trains and stations for the form dropdowns
$trains = $conn->query("SELECT id, train_name FROM trains ORDER BY train_name");
$stations = $conn->query("SELECT id, name FROM stations ORDER BY name");

// Handle add schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $train_id = (int)$_POST['train_id'];
    $departure_station_id = (int)$_POST['departure_station_id'];
    $arrival_station_id = (int)$_POST['arrival_station_id'];
    $departure_time = $conn->real_escape_string($_POST['departure_time']);  // 'HH:MM'
    $arrival_time = $conn->real_escape_string($_POST['arrival_time']);      // 'HH:MM'
    $journey_date = $conn->real_escape_string($_POST['journey_date']);      // 'YYYY-MM-DD'

    if ($departure_station_id !== $arrival_station_id) {
        $departure_datetime = $journey_date . ' ' . $departure_time . ':00';
        $arrival_datetime = $journey_date . ' ' . $arrival_time . ':00';

        $insert_schedule_sql = "INSERT INTO schedules (train_id, departure_station_id, arrival_station_id, departure_time, arrival_time, journey_date)
                                VALUES ($train_id, $departure_station_id, $arrival_station_id, '$departure_datetime', '$arrival_datetime', '$journey_date')";

        if ($conn->query($insert_schedule_sql) === TRUE) {
            $schedule_id = $conn->insert_id;

            // Get train classes for seat creation
            $train_classes_sql = "SELECT id, class_name, tickets_available FROM train_classes WHERE train_id = $train_id";
            $classes_result = $conn->query($train_classes_sql);

            if ($classes_result && $classes_result->num_rows > 0) {
                while ($class = $classes_result->fetch_assoc()) {
                    $class_id = (int)$class['id'];
                    $class_name = $conn->real_escape_string($class['class_name']);
                    $tickets_available = (int)$class['tickets_available'];

                    // Insert seats based on tickets_available
                    for ($i = 1; $i <= $tickets_available; $i++) {
                        // Example seat numbering: First class = 'F1', 'F2', etc.
                        $prefix = strtoupper(substr($class_name, 0, 1)); 
                        $seat_number = $prefix . $i;

                        $insert_seat_sql = "INSERT INTO seats (schedule_id, train_class_id, seat_number, class, is_available) 
                                            VALUES ($schedule_id, $class_id, '$seat_number', '$class_name', 1)";
                        $conn->query($insert_seat_sql);
                    }
                }
            }

            $message = "Schedule added and seats created successfully.";
        } else {
            $message = "Error adding schedule: " . $conn->error;
        }
    } else {
        $message = "Departure and arrival stations cannot be the same.";
    }
}

// Fetch existing schedules for display
$schedules_sql = "SELECT s.id, t.train_name, s.journey_date, ds.name AS departure_station, as1.name AS arrival_station, 
                  TIME(s.departure_time) AS departure_time, TIME(s.arrival_time) AS arrival_time 
                  FROM schedules s
                  JOIN trains t ON s.train_id = t.id
                  JOIN stations ds ON s.departure_station_id = ds.id
                  JOIN stations as1 ON s.arrival_station_id = as1.id
                  ORDER BY s.journey_date, s.departure_time";
$schedules_result = $conn->query($schedules_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Schedules</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Manage Schedules</h2>

    <?php if (!empty($message)) echo "<p style='color: green; font-weight: bold;'>$message</p>"; ?>

    <form method="POST" action="">
        <h3>Add New Schedule</h3>

        <label>Train:</label><br>
        <select name="train_id" required>
            <option value="">-- Select Train --</option>
            <?php while ($train = $trains->fetch_assoc()): ?>
                <option value="<?php echo $train['id']; ?>"><?php echo htmlspecialchars($train['train_name']); ?></option>
            <?php endwhile; ?>
        </select><br>

        <label>Departure Station:</label><br>
        <select name="departure_station_id" required>
            <option value="">-- Select Departure Station --</option>
            <?php while ($station = $stations->fetch_assoc()): ?>
                <option value="<?php echo $station['id']; ?>"><?php echo htmlspecialchars($station['name']); ?></option>
            <?php endwhile; ?>
        </select><br>

        <label>Arrival Station:</label><br>
        <select name="arrival_station_id" required>
            <option value="">-- Select Arrival Station --</option>
            <?php
            // Reset stations pointer for second dropdown
            $stations->data_seek(0);
            while ($station = $stations->fetch_assoc()): ?>
                <option value="<?php echo $station['id']; ?>"><?php echo htmlspecialchars($station['name']); ?></option>
            <?php endwhile; ?>
        </select><br>

        <label>Journey Date:</label><br>
        <input type="date" name="journey_date" required><br>

        <label>Departure Time:</label><br>
        <input type="time" name="departure_time" required><br>

        <label>Arrival Time:</label><br>
        <input type="time" name="arrival_time" required><br><br>

        <input type="submit" name="add_schedule" value="Add Schedule">
    </form>

    <h3>Existing Schedules</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Train Name</th>
            <th>Journey Date</th>
            <th>Departure Station</th>
            <th>Arrival Station</th>
            <th>Departure Time</th>
            <th>Arrival Time</th>
        </tr>
        <?php while ($row = $schedules_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['train_name']); ?></td>
            <td><?php echo $row['journey_date']; ?></td>
            <td><?php echo htmlspecialchars($row['departure_station']); ?></td>
            <td><?php echo htmlspecialchars($row['arrival_station']); ?></td>
            <td><?php echo date("H:i", strtotime($row['departure_time'])); ?></td>
            <td><?php echo date("H:i", strtotime($row['arrival_time'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>
