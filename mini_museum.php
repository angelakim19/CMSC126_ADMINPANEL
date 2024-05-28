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

// Function to calculate the time when the user will leave
function calculateEndTime($startTime, $hours) {
    $endTime = strtotime($startTime) + ($hours * 3600); // Convert hours to seconds
    return date('Y-m-d H:i:s', $endTime);
}

// Define the number of tables
$numTables = 2;

// Define the number of chairs for each table
$tableChairs = array(7, 7);

// Displaying reservations for each table in the admin panel
echo "<h2>Museum Reservations</h2>";

for ($i = 1; $i <= $numTables; $i++) {
    echo "<h3>Table $i</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Reservation ID</th><th>Reserved By</th><th>Reservation Time</th><th>Hours</th><th>End Time</th><th>Chairs</th><th>Occupied Seats</th><th>Available Seats</th><th>Total Seats</th><th>Action</th></tr>";

    // Fetch and calculate occupied seats for the current table
    $sqlOccupied = "SELECT SUM(chairs) AS occupied_seats FROM museum_reservations WHERE table_number = $i";
    $resultOccupied = $conn->query($sqlOccupied);

    if ($resultOccupied->num_rows > 0) {
        $rowOccupied = $resultOccupied->fetch_assoc();
        $occupiedSeats = $rowOccupied['occupied_seats'];
    } else {
        $occupiedSeats = 0;
    }

    // Calculate total seats for the current table
    $totalSeats = $tableChairs[$i - 1];

    // Calculate available seats for the current table
    $availableSeats = $totalSeats - $occupiedSeats;

    // SQL query to fetch reservations for the current table
    $sql = "SELECT museum_reservations.*, CONCAT(users.lastname, ', ', users.firstname, ' ', users.middlename) AS fullname 
            FROM museum_reservations 
            JOIN users ON museum_reservations.reserved_by = users.id 
            WHERE museum_reservations.table_number = $i";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $endTime = calculateEndTime($row["reservation_time"], $row["hour"]); // Calculate end time
            echo "<tr>";
            echo "<td>" . $row["reservation_id"] . "</td>";
            echo "<td>" . $row["fullname"] . "</td>";
            echo "<td>" . $row["reservation_time"] . "</td>";
            echo "<td>" . $row["hour"] . "</td>";
            echo "<td>" . $endTime . "</td>";
            echo "<td>" . $row["chairs"] . "</td>";
            echo "<td>" . $occupiedSeats . "</td>"; // Occupied seats
            echo "<td>" . $availableSeats . "</td>"; // Available seats
            echo "<td>" . $totalSeats . "</td>"; // Total Seats
            
            // Edit and Delete buttons
            echo "<td><a href='edit_museum_reservation.php?reservation_id=" . $row['reservation_id'] . "'><button>Edit</button></a> <br>
            <a href='delete_museum_reservation.php?reservation_id=" . $row['reservation_id'] . "'><button>Delete</button></a> <br></td>";

            echo "</tr>"; // Close the table row
        }
    } else {
        echo "<td colspan='10'>No reservations for this table</td>";
    }
    
    // Close the table
    echo "</table><br>";
}

echo "<div><br><button onclick=\"window.location.href = 'add_museum_reservation.php'\">Add New Reservation</button></div>";

echo "<div><br><button onclick=\"window.location.href = 'library_places.html'\">Library Places</button> 

<button onclick=\"window.location.href = 'admin_dashboard.html'\">Admin Dashboard</button>

</div>";

// Close connection
$conn->close();
?>
