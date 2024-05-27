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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO coffeelibro_reservations (reserved_by, reservation_time, hour, end_time, chairs, table_number) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissi", $reserved_by, $reservation_time, $hour, $end_time, $chairs, $table_number);

    // Set parameters and execute
    $reserved_by = $_POST['reserved_by'];
    $reservation_time = $_POST['reservation_time'];
    $hour = $_POST['hour'];
    $end_time = date('Y-m-d H:i:s', strtotime($reservation_time) + ($hour * 3600));
    $chairs = $_POST['chairs'];
    $table_number = $_POST['table_number'];

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
    <title>Add Coffee Reservation</title>
</head>
<body>
    <h2>Add Coffee Reservation</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="reserved_by">Reserved By:</label>
        <select id="reserved_by" name="reserved_by" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo $user['fullname']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="reservation_time">Reservation Time:</label>
        <input type="datetime-local" id="reservation_time" name="reservation_time" required><br><br>

        <label for="hour">Hour:</label>
        <input type="number" id="hour" name="hour" required><br><br>

        <label for="chairs">Chairs:</label>
        <input type="number" id="chairs" name="chairs" min="1" value="1" required><br><br>

        <!-- Dynamic table number selection -->
        <label for="table_number">Table Number:</label>
        <select id="table_number" name="table_number" required>
            <option value="1">Table 1</option>
            <option value="2">Table 2</option>
            <option value="3">Table 3</option>
        </select><br><br>

        <input type="submit" value="Add Reservation">
    </form>
</body>
</html>
