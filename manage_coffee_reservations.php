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

// Fetch coffee reservations from the database, joining with users table to get reserved_by name
$sql = "SELECT r.reservation_id, CONCAT(u.lastname, ', ', u.firstname) AS reserved_by_name, r.reservation_time, r.hour, r.end_time, r.chairs, r.table_number 
        FROM coffeelibro_reservations r
        INNER JOIN users u ON r.reserved_by = u.id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coffee Reservations</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Cafe Libro Reservations</h2>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>Reserved By</th>
            <th>Reservation Time</th>
            <th>Hour</th>
            <th>End Time</th>
            <th>Chairs</th>
            <th>Table Number</th>
            <th>Action</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["reservation_id"] . "</td>";
                echo "<td>" . $row["reserved_by_name"] . "</td>";
                echo "<td>" . $row["reservation_time"] . "</td>";
                echo "<td>" . $row["hour"] . "</td>";
                echo "<td>" . $row["end_time"] . "</td>";
                echo "<td>" . $row["chairs"] . "</td>";
                echo "<td>" . $row["table_number"] . "</td>";
                echo "<td><a href='edit_coffee_reservation.php?reservation_id=" . $row['reservation_id'] . "'>Edit</a> | <a href='delete_coffee_reservation.php?reservation_id=" . $row['reservation_id'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No reservations found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
