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

// Function to release expired reservations and update available seats
function releaseExpiredReservations($conn) {
    // Get current date and time
    $currentDateTime = date('Y-m-d H:i:s');

    // SQL query to identify expired reservations
    $sql = "SELECT * FROM museum_reservations WHERE end_time < '$currentDateTime'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Get the number of chairs reserved in the expired reservation
            $reservedChairs = $row["chairs"];

            // Get the table number for the expired reservation
            $tableNumber = $row["table_number"];

            // Update the available seats by releasing the chairs
            $sqlUpdate = "UPDATE museum_reservations SET available = available + $reservedChairs WHERE table_number = $tableNumber";
            $conn->query($sqlUpdate);

            // Remove the expired reservation from the database
            $reservationId = $row['reservation_id'];
            $sqlDelete = "DELETE FROM museum_reservations WHERE reservation_id = $reservationId";
            $conn->query($sqlDelete);
        }
    }
}

// Call the function to release expired reservations
releaseExpiredReservations($conn);

// Define the number of tables
$numTables = 2;

// Define the number of chairs for each table
$tableChairs = array(7, 7);

// Initialize arrays to store occupied and available seats for each table
$occupiedSeats = array();
$availableSeats = array();

// Fetch and calculate occupied and available seats for each table
for ($i = 1; $i <= $numTables; $i++) {
    $occupiedSeats[$i] = 0;
    $availableSeats[$i] = $tableChairs[$i - 1]; // Subtracting 1 to match array index

    // SQL query to fetch reservations for the current table
    $sql = "SELECT museum_reservations.*, CONCAT(users.lastname, ', ', users.firstname, ' ', users.middlename) AS fullname 
            FROM museum_reservations 
            JOIN users ON museum_reservations.reserved_by = users.id 
            WHERE museum_reservations.table_number = $i";
    $result = $conn->query($sql);

    // Calculate occupied seats for the current table
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $occupiedSeats[$i] += $row["chairs"];
        }
        // Calculate available seats for the current table
        $availableSeats[$i] -= $occupiedSeats[$i];
    }
}

// Displaying reservations for each table in the admin panel
echo "<h2>Museum Reservations</h2>";

for ($i = 1; $i <= $numTables; $i++) {
    echo "<h3>Table $i</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Reservation ID</th><th>Reserved By</th><th>Reservation Time</th><th>Hours</th><th>End Time</th><th>Chairs</th><th>Occupied Seats</th><th>Available Seats</th><th>Total Seats</th><th>Action</th></tr>";

    // Calculate total seats for the current table
    $totalSeats = $tableChairs[$i - 1];

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
            echo "<td>" . $occupiedSeats[$i] . "</td>"; // Occupied seats
            echo "<td>" . $availableSeats[$i] . "</td>"; // Available seats
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
