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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Libro Reservations</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .photo-icons {
            display: flex;
            padding: 10px 10px;
            cursor: pointer;
            border-radius: 50px;
        }

        .photo-icon {
            display: flex;
            height: 40px;
            width: 40px;
            margin-right: 18px;
        }

        h1 {
            font-family: 'Quiapo', sans-serif;
            font-size: 100px;
            color: #070707;
            margin: 0 0 0 400px;
            margin-top: 30px;
            margin-left: 60px;
        }


        .tnum {
            font-family: 'Quiapo', sans-serif;
            font-size: 50px;
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            padding: 6px;
            margin-left: 60px;
        }

        .table-container {
            position: relative;
            width: 30%;
            padding: 50px;
            margin: auto; /* Center the table */
            display: flex;
            flex-direction: column;
            align-items: center; /* Align items vertically */
        }
        

        table {
            width: 30%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-right: auto; /* Set right margin to auto */
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .buttons {
            margin: 20px 20px; /* Adjusted margin */
            margin-left: auto; /* Move buttons to the right */
            display: flex; /* Use flexbox for alignment */
            justify-content: flex-end; /* Align items to the end (right side) */
        }

        .buttons button {
            padding: 10px 20px;
            margin-right: 10px;
            font-size: 16px;
            border: 2px solid black;
            border-radius: 5px;
            background-color: #535151;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }

        .buttons button:hover {
            background-color: #45a049;
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        tr {
            background-color: #f1f1f1;
        }

        .action-links {
            display: flex;
            flex-direction: column; /* Align links vertically */
        }
        

        .edit-link {
            display: inline-block;
            margin-bottom: 5px;
            color: #fff; /* White text color */
            background-color: #007bff; /* Blue background color */
            border: 2px solid #020303; /* Blue border */
            border-radius: 5px; /* Rounded corners */
            padding: 8px 16px; /* Padding */
            margin-right: 10px; /* Adjust spacing between links */
            text-decoration: none;
        }
        
        .delete-link {
            display: inline-block;
            margin-bottom: 5px;
            color: #fff; /* White text color */
            background-color: #dc3545; /* Red background color */
            border: 2px solid #080707; /* Red border */
            border-radius: 5px; /* Rounded corners */
            padding: 8px 16px; /* Padding */
            text-decoration: none;
        }
        
        .edit-link:hover,
        .delete-link:hover {
            background-color: #00b31e; /* Darker blue on hover */
            border-color: #040505; /* Darker blue border on hover */
        }

        .button {
            padding: 10px 20px;
            margin-right: 10px;
            font-size: 16px;
            border: 2px solid black;
            border-radius: 5px;
            background-color: #535151;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }
        
        .button:hover {
            background-color: #45a049;
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .add, .backbuttons {
            margin: 20px 30px 10px 0;
            display: flex;
            justify-content: right;
        }

        .add button, .backbuttons button {
            padding: 10px 20px;
            margin: 0 10px;
            font-size: 16px;
            border: 2px solid black;
            border-radius: 5px;
            background-color: #535151;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }

        .add button:hover, .backbuttons button:hover {
            background-color: #45a049;
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <header>
        <div class="header-text">
            <a href="admin_loginlandingpage.html">
                <div class="logo-container"></div>
            </a>
            <div class="header-title">
                <h1>YUPI</h1>
                <h5>UP Mindanao Library Log</h5>
            </div>
        </div>
        <div class="photo-icons">
          <img src="bell.png" class="photo-icon">
          <img src="option.png" class="photo-icon">
        </div>
    </header>

    <?php
    // Displaying reservations for each table in the admin panel
    echo "<h2>Reading Area Reservations</h2>";
    echo "<div style='display:flex;'>";

    // Display tables 1-5 on the left side
    echo "<div class='table-container' style='flex: 1;'>";
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
    echo "<div class='table-container' style='flex: 1;'>";
    for ($i = 6; $i <= 10; $i++) {
        // Table display logic here
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
                echo "<td><a href='edit_reading_area.php?reservation_id=" . $row['reservation_id'] . "'><button class='edit-link'>Edit</button></a> 
                <br>
                <a href='delete_reading_area.php?reservation_id=" . $row['reservation_id'] . "'><button class='delete-link'>Delete</button></a> 
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
    echo "<div class='add'>";
    echo "<button onclick=\"window.location.href = 'add_museum_reservation.php'\">Add New Reservation</button>";
    echo "</div>";

    // Add the "Back" buttons
    echo "<div class='backbuttons'>";
    echo "<button onclick=\"window.location.href = 'library_places.html'\">Library Places</button>";
    echo "<button onclick=\"window.location.href = 'admin_dashboard.html'\">Admin Dashboard</button>";
    echo "</div>";
    ?>

    <footer id="footer">
        <div class="fleft">
            <img src="Oble2.png" alt="Oblation2" class="oble2">
            <img src="UPMInLogo.png" alt="UP Mindanao Logo" class="fupmlogo">
            <img src="yupilogo.png" alt="YUPI Logo" class="fyupilogo">
        </div>

        <div class="fmiddle">
            <h3>University of the Philippines Mindanao</h3>
            <h5>The University Library, UP Mindanao, Mintal, Tugbok District, Davao City, Philippines</h5>
            <h5>Contact: (082)295-7025</h5>
            <h5>Email: library.upmindanao@up.edu.ph</h5>
            <h5>&copy; 2024 University Library, University of the Philippines Mindanao. All Rights Reserved.</h5>
        </div>

        <div class="fright">
            <h4>Quick List</h4>
            <a href="https://alarm.upmin.edu.ph/" >UP Mindanao ALARM</a>
        </div>
    </footer>

    <script>
        // Function to navigate back to the admin dashboard
        function goBackone() {
            window.location.href = "admin_dashboard.html"; // Replace with the actual URL of your admin dashboard
        }

        function goBacktwo() {
            window.location.href = "user_information_adminpanel.php";
        }
    </script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
