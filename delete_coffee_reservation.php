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

// Check if reservation_id is set in the URL
if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];

    // Prepare and bind
    $sql = "DELETE FROM coffeelibro_reservations WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to cafe_libro.php
        header("Location: cafe_libro.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
} else {
    echo "No reservation ID provided.";
}

// Close connection
$conn->close();
?>
