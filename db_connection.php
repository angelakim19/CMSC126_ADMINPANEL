<?php
$servername = "localhost";  // The hostname of the database server
$username = "root";  // The username to connect to the database
$password = "your_password";  // The password to connect to the database
$dbname = "registration_db";  // The name of the database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

