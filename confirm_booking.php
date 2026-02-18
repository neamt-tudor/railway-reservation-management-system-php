<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$schedule_id = $_POST['schedule_id'];
$seat_id = $_POST['seat_id'];
$booking_date = date('Y-m-d H:i:s');
$status = 'Confirmed';
$payment_method = 'Credit Card'; // For simplicity
$payment_status = 'Paid';
$amount = 50; // Flat rate, or fetch based on class

// Start transaction
$conn->begin_transaction();

try {
    // 1. Create booking
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, schedule_id, booking_date, total_price, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $schedule_id, $booking_date, $amount, $status);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // 2. Mark seat as unavailable
    $stmt = $conn->prepare("UPDATE seats SET is_available = 0 WHERE id = ?");
    $stmt->bind_param("i", $seat_id);
    $stmt->execute();
    $stmt->close();

    // 3. Add payment record
    $payment_date = $booking_date;
    $stmt = $conn->prepare("INSERT INTO payments (booking_id, payment_method, payment_status, amount, payment_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $booking_id, $payment_method, $payment_status, $amount, $payment_date);
    $stmt->execute();
    $stmt->close();

    // 4. Generate ticket
    $pnr = strtoupper(substr(md5(uniqid()), 0, 8));
    $issue_date = date('Y-m-d');
    $stmt = $conn->prepare("INSERT INTO tickets (booking_id, pnr_number, issue_date, seat_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $booking_id, $pnr, $issue_date, $seat_id);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo "<div style='text-align:center; padding:20px;'>";
    echo "<h2>Booking Successful!</h2>";
    echo "<p>Your ticket id is: <strong>$pnr</strong></p>";
    echo "<a href='dashboard.php'>Back to Dashboard</a>";
    echo "</div>";

} catch (Exception $e) {
    $conn->rollback();
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
