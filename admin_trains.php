<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$message = "";

// Handle add train form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_train'])) {
    $train_number = $conn->real_escape_string($_POST['train_number']);
    $train_name = $conn->real_escape_string($_POST['train_name']);
    $train_type = $conn->real_escape_string($_POST['train_type']);
    $capacity = (int)$_POST['capacity'];

    $insert_train_sql = "INSERT INTO trains (train_number, train_name, train_type, capacity) VALUES ('$train_number', '$train_name', '$train_type', $capacity)";
    if ($conn->query($insert_train_sql) === TRUE) {
        $train_id = $conn->insert_id;

        // Insert train classes (up to 3)
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($_POST["class_name$i"]) && $_POST["tickets_available$i"] !== '') {
                $class_name = $conn->real_escape_string($_POST["class_name$i"]);
                $tickets_available = (int)$_POST["tickets_available$i"];

                $insert_class_sql = "INSERT INTO train_classes (train_id, class_name, tickets_available) VALUES ($train_id, '$class_name', $tickets_available)";
                $conn->query($insert_class_sql);
            }
        }

        $message = "Train and classes added successfully. Create a schedule to make it bookable.";
    } else {
        $message = "Error adding train: " . $conn->error;
    }
}

// Handle delete train
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_train_id'])) {
    $train_id = (int)$_POST['delete_train_id'];

    // Delete related seats for schedules of this train
    $result = $conn->query("SELECT id FROM schedules WHERE train_id = $train_id");
    while ($row = $result->fetch_assoc()) {
        $schedule_id = $row['id'];
        $conn->query("DELETE FROM seats WHERE schedule_id = $schedule_id");
    }

    // Delete classes, schedules and train
    $conn->query("DELETE FROM train_classes WHERE train_id = $train_id");
    $conn->query("DELETE FROM schedules WHERE train_id = $train_id");
    $conn->query("DELETE FROM trains WHERE id = $train_id");
}

// Fetch trains with their classes
$sql = "SELECT t.id, t.train_number, t.train_name, t.train_type, t.capacity,
        GROUP_CONCAT(CONCAT(tc.class_name, ' (', tc.tickets_available, ')') SEPARATOR ', ') AS classes
        FROM trains t
        LEFT JOIN train_classes tc ON t.id = tc.train_id
        GROUP BY t.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Trains</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Manage Trains</h2>

    <?php if (!empty($message)) echo "<p style='color: green; font-weight: bold;'>$message</p>"; ?>

    <form method="POST" action="">
        <h3>Add New Train</h3>

        <label>Train Number:</label><br>
        <input type="text" name="train_number" required><br>

        <label>Train Name:</label><br>
        <input type="text" name="train_name" required><br>

        <label>Train Type:</label><br>
        <input type="text" name="train_type" required><br>

        <label>Capacity:</label><br>
        <input type="number" name="capacity" min="1" required><br><br>

        <h4>Classes (Up to 3)</h4>
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <label>Class Name <?php echo $i; ?>:</label><br>
            <input type="text" name="class_name<?php echo $i; ?>"><br>
            <label>Tickets Available for Class <?php echo $i; ?>:</label><br>
            <input type="number" name="tickets_available<?php echo $i; ?>" min="0"><br><br>
        <?php endfor; ?>

        <input type="submit" name="add_train" value="Add Train">
    </form>

    <h3>Existing Trains</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Train Number</th>
            <th>Train Name</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Classes (Tickets Available)</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['train_number']); ?></td>
            <td><?php echo htmlspecialchars($row['train_name']); ?></td>
            <td><?php echo htmlspecialchars($row['train_type']); ?></td>
            <td><?php echo $row['capacity']; ?></td>
            <td><?php echo htmlspecialchars($row['classes']); ?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this train?');">
                    <input type="hidden" name="delete_train_id" value="<?php echo $row['id']; ?>">
                    <input type="submit" value="Delete Train">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
</div>
</body>
</html>
