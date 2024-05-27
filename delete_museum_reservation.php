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

// Check if reservation ID is set in the URL
if (!isset($_GET['reservation_id'])) {
    echo "Reservation ID not specified.";
    exit();
}

// Get reservation ID from the URL
$reservation_id = $_GET['reservation_id'];

// SQL query to delete the reservation
$sql_delete = "DELETE FROM museum_reservations WHERE reservation_id = $reservation_id";

if ($conn->query($sql_delete) === TRUE) {
    echo "Reservation deleted successfully";
} else {
    echo "Error deleting reservation: " . $conn->error;
}

// Close connection
$conn->close();

// Redirect back to mini_museum.php
header("Location: mini_museum.php");
exit();
?>
