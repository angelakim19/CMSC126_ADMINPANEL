<?php
// Include database connection
include 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission
    $reservation_id = $_POST['reservation_id'];
    $reserved_by = $_POST['reserved_by'];
    $reservation_time = $_POST['reservation_time'];
    $table_number = $_POST['table_number'];
    $chairs = $_POST['chairs'];
    $hours = $_POST['hour'];

    // Update the reservation details in the database
    $sql = "UPDATE reading_area SET reserved_by = ?, reservation_time = ?, table_number = ?, chairs = ?, hour = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("isiisi", $reserved_by, $reservation_time, $table_number, $chairs, $hours, $reservation_id);

    if ($stmt->execute()) {
        // Redirect to reading_area.php after successful update
        header("Location: reading_area.php");
        exit();
    } else {
        echo "Error updating reservation: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Fetch the reservation details for the given reservation ID
    if (!isset($_GET['reservation_id'])) {
        die("Reservation ID is required.");
    }
    
    $reservation_id = $_GET['reservation_id'];
    $sql = "SELECT * FROM reading_area WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No reservation found with ID $reservation_id";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation</title>
</head>
<body>
    <h2>Edit Reservation</h2>
    <form action="edit_reading_area.php" method="post">
        <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($row['reservation_id']); ?>">
        
        <label for="reserved_by">Reserved By:</label>
        <select name="reserved_by">
            <?php
            // Fetch user IDs and names from the users table
            $sql_users = "SELECT id, CONCAT(lastname, ', ', firstname, ' ', middlename) AS fullname FROM users";
            $result_users = $conn->query($sql_users);

            if ($result_users->num_rows > 0) {
                while($row_user = $result_users->fetch_assoc()) {
                    // Option value is the user ID, and the displayed text is the user's full name
                    echo "<option value='" . $row_user['id'] . "'";
                    if ($row['reserved_by'] == $row_user['id']) {
                        echo " selected"; // If this user is the one who made the reservation, select this option
                    }
                    echo ">" . htmlspecialchars($row_user['fullname']) . "</option>";
                }
            }
            ?>
        </select><br>
        
        <label for="reservation_time">Reservation Time:</label>
        <input type="datetime-local" name="reservation_time" value="<?php echo date('Y-m-d\TH:i', strtotime($row['reservation_time'])); ?>"><br>
        
        <label for="table_number">Table Number:</label>
        <input type="number" name="table_number" value="<?php echo htmlspecialchars($row['table_number']); ?>"><br>
        
        <label for="chairs">Chairs:</label>
        <input type="number" name="chairs" value="<?php echo htmlspecialchars($row['chairs']); ?>"><br>
        
        <label for="hour">Hours:</label>
        <input type="number" name="hour" value="<?php echo htmlspecialchars($row['hour']); ?>"><br>
        
        <input type="submit" value="Update Reservation">
    </form>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
