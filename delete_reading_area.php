<?php
// Include database connection
include 'db_connection.php';

// Check if reservation ID is provided
if (!isset($_GET['reservation_id'])) {
    die("Reservation ID is required.");
}

$reservation_id = $_GET['reservation_id'];

// Delete the reservation from the database
$sql = "DELETE FROM reading_area WHERE reservation_id = '$reservation_id'";
if ($conn->query($sql) === TRUE) {
    // Redirect to reading_area.php with success message
    header("Location: reading_area.php?deleted=true");
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}

// Close connection
$conn->close();
?>
