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

// Get reservation details from the form
$reservation_id = $_POST['reservation_id'];
$reserved_by = $_POST['reserved_by'];
$reservation_time = $_POST['reservation_time'];
$hour = $_POST['hour'];
$chairs = $_POST['chairs'];

// Update reservation in the database
$sql_update = "UPDATE museum_reservations SET reserved_by = '$reserved_by', reservation_time = '$reservation_time', hour = $hour, chairs = $chairs WHERE reservation_id = $reservation_id";

if ($conn->query($sql_update) === TRUE) {
    echo "Reservation updated successfully";
} else {
    echo "Error updating reservation: " . $conn->error;
}

// Close connection
$conn->close();

// Redirect back to mini_museum.php
header("Location: mini_museum.php");
exit();
?>
