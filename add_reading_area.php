<?php
// Include database connection
include 'db_connection.php';

// Include database connection
include 'db_connection.php';

// Fetch users from the database
$sql_users = "SELECT id, CONCAT(lastname, ', ', firstname, ' ', middlename) as fullname FROM users";
$result_users = $conn->query($sql_users);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $reserved_by = $_POST['reserved_by'];
    $table_number = $_POST['table_number'];
    $reservation_time = $_POST['reservation_time'];
    $max_duration = $_POST['hour'];

    // Insert new reservation into the database
    $sql = "INSERT INTO reading_area (reserved_by, table_number, reservation_time, hour) 
            VALUES ('$reserved_by', '$table_number', '$reservation_time', '$max_duration')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to reading_area.php after successful insertion
        header("Location: reading_area.php");
        exit();
    } else {
        // Handle errors
        echo "Error adding record: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>

<!-- HTML form for adding a new reservation -->
<h2>Add Reading Area Reservation</h2>
<form action="add_reading_area.php" method="post">
    <label for="reserved_by">Reserved By:</label>
    <select id="reserved_by" name="reserved_by" required>
        <option value="">Select User</option>
        <?php
        // Display users in the dropdown list
        if ($result_users->num_rows > 0) {
            while ($row = $result_users->fetch_assoc()) {
                echo "<option value='" . $row["id"] . "'>" . $row["fullname"] . "</option>";
            }
        }
        ?>
    </select>
    <br><br>
    <label for="table_number">Table Number:</label>
    <input type="number" id="table_number" name="table_number" min="1" max="20" required><br><br>
    <label for="reservation_time">Reservation Time:</label>
    <input type="datetime-local" id="reservation_time" name="reservation_time" required><br><br>
    <label for="hour">Max Duration (in hours):</label>
    <input type="number" id="hour" name="hour" required><br><br>
    <label for="chairs">Number of Chairs:</label>
    <input type="number" id="chairs" name="chairs" min="1" required><br><br>
    <input type="submit" value="Add Reservation">
</form>

<button onclick="location.href='reading_area.php'">Back to Reading Area Reservations</button>




