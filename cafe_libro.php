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

        h2 {
            font-family: 'Quiapo', sans-serif;
            font-size: 100px;
            color: #070707;
            margin: 0 0 0 400px;
            margin-top: 30px;
            margin-left: 320px;
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
            
        }

        table {
            width: 30%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-top: 20px;
           margin-left: 90px;
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
        echo "<div class='table-container'>";
        echo "<h4 class='tnum'>Table $tableNumber</h4>"; // Fixed the quotes here
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
                    <div class='action-links'>"; // Fixed the quotes here
                        echo "<a class='edit-link' href='edit_coffee_reservation.php?reservation_id=" . $row['reservation_id'] . "'>Edit</a> |"; // Fixed the quotes here
                        echo "<a class='delete-link' href='delete_coffee_reservation.php?reservation_id=" . $row['reservation_id'] . "'>Delete</a>"; // Fixed the quotes here
                echo "</div>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No reservations found</td></tr>";
        }

        // Close table
        echo "</table></div><br>";
    }
    ?>
    <div class="buttons">
        <button onclick="window.location.href = 'add_coffee_reservation.php';">Add</button>
        <button onclick="window.location.href = 'library_places.html';">Library Places</button>
    </div>

    <?php
    // Close database connection
    $conn->close();
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

  
    <script src="redirect.js"></script>
</body>
</html>
