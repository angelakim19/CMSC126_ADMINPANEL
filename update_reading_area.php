<?php
// Include database connection
include 'db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $reservation_id = $_POST['reservation_id'];
    $reserved_by = $_POST['reserved_by'];
    $table_number = $_POST['table_number'];
    $reservation_time = $_POST['reservation_time'];
    $max_duration = $_POST['max_duration'];

    // Update reservation in the database
    $sql = "UPDATE reading_area SET 
            reserved_by = '$reserved_by',
            table_number = '$table_number',
            reservation_time = '$reservation_time',
            max_duration = '$max_duration'
            WHERE reservation_id = '$reservation_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Redirect to reading_area.php
    header("Location: reading_area.php");
    exit();
}

// Close connection
$conn->close();
?>
