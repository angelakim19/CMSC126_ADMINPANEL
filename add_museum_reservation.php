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

// Fetch users from the database
$sql_users = "SELECT id, CONCAT(lastname, ', ', firstname, ' ', middlename) AS fullname FROM users";
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
    <title>Add Museum Reservation</title>
</head>
<body>
    <h2>Add Museum Reservation</h2>
    <form method="post" action="add_museum_reservation.php" id="reservation_form">
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
        </select><br><br>

        <input type="submit" value="Add Reservation">
    </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $reservedBy = $_POST['reserved_by'];
    $reservationTime = $_POST['reservation_time'];
    $hours = $_POST['hour'];
    $chairs = $_POST['chairs'];
    $tableNumber = $_POST['table_number'];

    // Calculate the end time
    function calculateEndTime($startTime, $hours) {
        $endTime = strtotime($startTime) + ($hours * 3600); // Convert hours to seconds
        return date('Y-m-d H:i:s', $endTime);
    }
    $endTime = calculateEndTime($reservationTime, $hours);

    // Insert the new reservation into the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "INSERT INTO museum_reservations (reserved_by, reservation_time, hour, end_time, chairs, table_number) 
            VALUES ('$reservedBy', '$reservationTime', '$hours', '$endTime', '$chairs', '$tableNumber')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New reservation added successfully";
        header("Location: mini_museum.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
