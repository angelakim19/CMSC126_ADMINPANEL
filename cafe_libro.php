<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Libro Reservations</title>
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

    // Function to calculate the end time based on reservation time and duration
    function calculateEndTime($reservationTime, $hours) {
        $endTime = strtotime($reservationTime) + ($hours * 3600); // Convert hours to seconds
        return date('Y-m-d H:i:s', $endTime);
    }

    // Loop through each table
    for ($tableNumber = 1; $tableNumber <= 3; $tableNumber++) {
        // Fetch reservations for the current table
        $sql = "SELECT r.reservation_id, CONCAT(u.lastname, ', ', u.firstname) AS reserved_by_name, r.reservation_time, r.hour, r.chairs, r.table_number 
                FROM coffeelibro_reservations r
                INNER JOIN users u ON r.reserved_by = u.id
                WHERE r.table_number = $tableNumber";
        $result = $conn->query($sql);

        // Display table header
        echo "<h3>Table $tableNumber</h3>";
        echo "<table>";
        echo "<tr>
                <th>Reservation ID</th>
                <th>Reserved By</th>
                <th>Reservation Time</th>
                <th>Hour</th>
                <th>End Time</th>
                <th>Chairs</th>
                <th>Action</th>
            </tr>";

        // Display reservations for the current table
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Calculate end time
                $endTime = calculateEndTime($row["reservation_time"], $row["hour"]);
                echo "<tr>";
                echo "<td>" . $row["reservation_id"] . "</td>";
                echo "<td>" . $row["reserved_by_name"] . "</td>";
                echo "<td>" . $row["reservation_time"] . "</td>";
                echo "<td>" . $row["hour"] . "</td>";
                echo "<td>" . $endTime . "</td>";
                echo "<td>" . $row["chairs"] . "</td>";
                echo "<td>
                        <a href='edit_coffee_reservation.php?reservation_id=" . $row['reservation_id'] . "'>Edit</a> |
                        <a href='delete_coffee_reservation.php?reservation_id=" . $row['reservation_id'] . "'>Delete</a>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No reservations found</td></tr>";
        }

        // Close table
        echo "</table><br>";
    }
    ?>

    <button onclick="window.location.href = 'add_coffee_reservation.php';">Add</button>
    <button onclick="window.location.href = 'library_places.html';">Library Places</button>


    <?php
    // Close database connection
    $conn->close();
    ?>
</body>
</html>
