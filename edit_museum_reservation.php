<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Museum Reservation</title>
</head>
<body>
    <h2>Edit Museum Reservation</h2>
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
        exit(); // Stop execution if no users are found
    }

    // Fetch reservation details based on reservation ID
    $reservation_id = $_GET['reservation_id'];
    $sql_reservation = "SELECT * FROM museum_reservations WHERE reservation_id = $reservation_id";
    $result_reservation = $conn->query($sql_reservation);
    if ($result_reservation->num_rows > 0) {
        $row = $result_reservation->fetch_assoc();
        $reserved_by = $row['reserved_by'];
        $reservation_time = $row['reservation_time'];
        $hour = $row['hour'];
        $chairs = $row['chairs'];
    } else {
        echo "Reservation not found.";
        exit();
    }

    // Close connection
    $conn->close();
    ?>
    <form method="post" action="update_museum_reservation.php">
        <input type="hidden" name="reservation_id" value="<?php echo $reservation_id; ?>">
        <label for="reserved_by">Reserved By:</label>
        <select id="reserved_by" name="reserved_by" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>" <?php if ($user['id'] == $reserved_by) echo 'selected'; ?>><?php echo $user['fullname']; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="reservation_time">Reservation Time:</label>
        <input type="datetime-local" id="reservation_time" name="reservation_time" value="<?php echo $reservation_time; ?>" required><br><br>

        <label for="hour">Hour:</label>
        <input type="number" id="hour" name="hour" value="<?php echo $hour; ?>" required><br><br>

        <label for="chairs">Chairs:</label>
        <input type="number" id="chairs" name="chairs" min="1" value="<?php echo $chairs; ?>" required><br><br>

        <input type="submit" name="submit" value="Update Reservation">
    </form>
    <button onclick="window.location.href = 'mini_museum.php'">Cancel</button>
</body>
</html>
