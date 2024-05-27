<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "registration_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch reservation details for the given reservation_id
if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];
    $sql = "SELECT * FROM coffeelibro_reservations WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission to update reservation details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST['reservation_id'];
    $reserved_by = $_POST['reserved_by'];
    $reservation_time = $_POST['reservation_time'];
    $hour = $_POST['hour'];
    $end_time = date('Y-m-d H:i:s', strtotime($reservation_time) + ($hour * 3600));
    $chairs = $_POST['chairs'];
    $table_number = $_POST['table_number'];

    $sql = "UPDATE coffeelibro_reservations SET reserved_by = ?, reservation_time = ?, hour = ?, end_time = ?, chairs = ?, table_number = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isissii", $reserved_by, $reservation_time, $hour, $end_time, $chairs, $table_number, $reservation_id);

    if ($stmt->execute()) {
        // Redirect back to cafe_libro.php
        header("Location: cafe_libro.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch users from the database
$sql_users = "SELECT id, CONCAT(lastname, ', ', firstname) AS fullname FROM users";
$result_users = $conn->query($sql_users);

// Check if users are fetched successfully
if ($result_users->num_rows > 0) {
    // Store users in an array
    $users = [];
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "No users found";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Coffee Reservation</title>
</head>
<body>
    <h2>Edit Coffee Reservation</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">

        <label for="reserved_by">Reserved By:</label>
        <select id="reserved_by" name="reserved_by" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>" <?php if ($user['id'] == $reservation['reserved_by']) echo 'selected'; ?>>
                    <?php echo $user['fullname']; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="reservation_time">Reservation Time:</label>
        <input type="datetime-local" id="reservation_time" name="reservation_time" value="<?php echo date('Y-m-d\TH:i', strtotime($reservation['reservation_time'])); ?>" required><br><br>

        <label for="hour">Hour:</label>
        <input type="number" id="hour" name="hour" value="<?php echo $reservation['hour']; ?>" required><br><br>

        <label for="chairs">Chairs:</label>
        <input type="number" id="chairs" name="chairs" min="1" value="<?php echo $reservation['chairs']; ?>" required><br><br>

        <!-- Dynamic table number selection -->
        <label for="table_number">Table Number:</label>
        <select id="table_number" name="table_number" required>
            <option value="1" <?php if ($reservation['table_number'] == 1) echo 'selected'; ?>>Table 1</option>
            <option value="2" <?php if ($reservation['table_number'] == 2) echo 'selected'; ?>>Table 2</option>
            <option value="3" <?php if ($reservation['table_number'] == 3) echo 'selected'; ?>>Table 3</option>
        </select><br><br>

        <input type="submit" value="Update Reservation">
    </form>
</body>
</html>
