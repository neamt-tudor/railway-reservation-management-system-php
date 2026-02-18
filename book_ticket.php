<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

// Fetch available schedules
$sql = "SELECT s.id AS schedule_id, t.train_name, s.journey_date, ds.name AS departure_station, 
               as1.name AS arrival_station, s.departure_time, s.arrival_time 
        FROM schedules s
        JOIN trains t ON s.train_id = t.id
        JOIN stations ds ON s.departure_station_id = ds.id
        JOIN stations as1 ON s.arrival_station_id = as1.id
        ORDER BY s.journey_date, s.departure_time";
$schedules_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Ticket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Book a Ticket</h2>
    <form action="confirm_booking.php" method="POST">
        <label for="schedule">Select Schedule:</label><br>
        <select name="schedule_id" id="schedule" required>
            <option value="">-- Select Schedule --</option>
            <?php
            while ($row = $schedules_result->fetch_assoc()) {
                $departure_time = date("H:i", strtotime($row['departure_time']));
                $label = "{$row['train_name']} - {$row['journey_date']} ({$row['departure_station']} → {$row['arrival_station']} at {$departure_time})";
                echo "<option value='{$row['schedule_id']}'>" . htmlspecialchars($label) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="class">Select Class:</label><br>
        <select name="seat_class" id="class" required>
            <option value="">-- Select Class --</option>
            <option value="First">First</option>
            <option value="Second">Second</option>
            <option value="Third">Third</option>
        </select><br><br>

        <label for="seat">Select Seat Number:</label><br>
        <select name="seat_id" id="seat" required>
            <option value="">Select schedule and class first</option>
        </select><br><br>

        <input type="submit" value="Book Ticket">
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</div>

<script>
const scheduleSelect = document.getElementById('schedule');
const classSelect = document.getElementById('class');
const seatSelect = document.getElementById('seat');

function fetchSeats() {
    const scheduleId = scheduleSelect.value;
    const selectedClass = classSelect.value;

    if (!scheduleId || !selectedClass) {
        seatSelect.innerHTML = '<option>Select schedule and class first</option>';
        return;
    }

    seatSelect.innerHTML = '<option>Loading seats...</option>';

    fetch(`fetch_seats.php?schedule_id=${scheduleId}&class=${selectedClass}`)
        .then(response => response.json())
        .then(data => {
            seatSelect.innerHTML = '';
            if (data.length === 0) {
                seatSelect.innerHTML = '<option>No seats available</option>';
                return;
            }
            data.forEach(seat => {
                const option = document.createElement('option');
                option.value = seat.id;
                option.text = seat.seat_number;
                seatSelect.appendChild(option);
            });
        })
        .catch(() => {
            seatSelect.innerHTML = '<option>Error loading seats</option>';
        });
}

scheduleSelect.addEventListener('change', fetchSeats);
classSelect.addEventListener('change', fetchSeats);
</script>

</body>
</html>
