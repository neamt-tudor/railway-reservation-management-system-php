<?php
include 'db.php';

$schedule_id = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : 0;
$seat_class = isset($_GET['class']) ? $_GET['class'] : '';

if ($schedule_id <= 0 || empty($seat_class)) {
    // Invalid input, return empty JSON array
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, seat_number FROM seats 
        WHERE schedule_id = ? AND class = ? AND is_available = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $schedule_id, $seat_class);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = $row;
}

header('Content-Type: application/json');
echo json_encode($seats);
?>
