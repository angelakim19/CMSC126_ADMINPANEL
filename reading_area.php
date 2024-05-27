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
    $sql = "SELECT * FROM reading_area WHERE end_time < '$currentDateTime'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Get the number of chairs reserved in the expired reservation
            $reservedChairs = $row["chairs"];

            // Get the table number for the expired reservation
            $tableNumber = $row["table_number"];

            // Update the available seats by releasing the chairs
            $sqlUpdate = "UPDATE reading_area SET available = available + $reservedChairs WHERE table_number = $tableNumber";
            $conn->query($sqlUpdate);

            // Remove the expired reservation from the database
            $reservationId = $row['reservation_id'];
            $sqlDelete = "DELETE FROM reading_area WHERE reservation_id = $reservationId";
            $conn->query($sqlDelete);
        }
    }
}

// Call the function to release expired reservations
releaseExpiredReservations($conn);

// Define the number of tables
$numTables = 10;

// Define the number of chairs for each table
$tableChairs = array(6, 2, 8, 6, 8, 3, 1, 1, 1, 1);

// Initialize arrays to store occupied and available seats for each table
$occupiedSeats = array();
$availableSeats = array();

// Fetch and calculate occupied and available seats for each table
for ($i = 1; $i <= $numTables; $i++) {
    $occupiedSeats[$i] = 0;
    $availableSeats[$i] = $tableChairs[$i - 1]; // Subtracting 1 to match array index

    // SQL query to fetch reservations for the current table
    $sql = "SELECT * FROM reading_area WHERE table_number = $i";
    $result = $conn->query($sql);

    // Calculate occupied seats for the current table
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $occupiedSeats[$i] += $row["chairs"];
        }
        // Calculate available seats for the current table
        $availableSeats[$i] -= $occupiedSeats[$i];
    }
}


// Displaying reservations for each table in the admin panel
echo "<h2>Reading Area Reservations</h2>";
echo "<div style='display:flex;'>";

// Display tables 1-5 on the left side
echo "<div style='flex: 1;'>";
for ($i = 1; $i <= 5; $i++) {
    echo "<h3>Table $i</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Reservation ID</th><th>Reserved By</th><th>Reservation Time</th><th>Hours</th><th>End Time</th><th>Chairs</th><th>Occupied Seats</th><th>Available Seats</th><th>Total Seat</th><th>Action</th></tr>";

    // Calculate total seats for the current table
    $totalSeats = $tableChairs[$i - 1];

    // SQL query to fetch reservations for the current table
    $sql = "SELECT r.*, CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) as fullname FROM reading_area r JOIN users u ON r.reserved_by = u.id WHERE table_number = $i";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
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
            echo "<td><a href='edit_reading_area.php?reservation_id=" . $row['reservation_id'] . "'><button>Edit</button></a> <br>
            <a href='delete_reading_area.php?reservation_id=" . $row['reservation_id'] . "'><button>Delete</button></a> <br></td>";

            echo "</tr>"; // Close the table row
        }
    } else {
        echo "<td colspan='8'>No reservations for this table</td>";
    }

    // Close the table
    echo "</table><br>";
}
echo "</div>";

// Display tables 6-10 on the right side
echo "<div style='flex: 1;'>";
for ($i = 6; $i <= 10; $i++) {
    echo "<h3>Table $i</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Reservation ID</th><th>Reserved By</th><th>Reservation Time</th><th>Hours</th><th>End Time</th><th>Chairs</th><th>Occupied Seats</th><th>Available Seats</th><th>Total Seat</th><th>Action</th></tr>";

    // Calculate total seats for the current table
    $totalSeats = $tableChairs[$i - 1];

    // SQL query to fetch reservations for the current table
    $sql = "SELECT r.*, CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) as fullname FROM reading_area r JOIN users u ON r.reserved_by = u.id WHERE table_number = $i";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
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
            echo "<td><a href='edit_reading_area.php?reservation_id=" . $row['reservation_id'] . "'><button>Edit</button></a> 
            <br>
            <a href='delete_reading_area.php?reservation_id=" . $row['reservation_id'] . "'><button>Delete</button></a> 
            <br>";

            echo "</tr>"; // Close the table row
        }
    } else {
        echo "<tr><td colspan='8'>No reservations for this table</td></tr>";
    }

    // Close the table
    echo "</table><br>";
    
}
echo "</div>";


// Close the flex container
echo "</div>";

// Add the "Add" button below all the tables
echo "<div style='text-align: center;'>";
echo "<a href='add_reading_area.php'><button>Add</button></a><br><br>";
echo "</div>";

echo "<div style='text-align: center;'>";
echo "<a href='Library_places.html'><button>Back to Library Spaces</button></a>";
echo "</div>";

// Close connection
$conn->close();
?>