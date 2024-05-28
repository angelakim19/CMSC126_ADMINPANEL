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

// SQL query to fetch user activity data
$sql = "
    SELECT 
        u.id AS user_id, 
        CONCAT(u.lastname, ', ', u.firstname, ' ', u.middlename) AS user_name, 
        cl.reservation_time AS cl_reservation_time, 
        cl.hour AS cl_hour, 
        DATE_ADD(cl.reservation_time, INTERVAL cl.hour HOUR) AS cl_end_time, 
        cl.chairs AS cl_chairs, 
        cl.table_number AS cl_table_number, 
        br.borrowed_date AS br_borrowed_date, 
        br.due_date AS br_due_date,
        CASE 
            WHEN br.due_date < NOW() THEN 'overdue' 
            ELSE 'borrowed' 
        END AS borrowed_status,
        mr.reservation_time AS mr_reservation_time,
        mr.hour AS mr_hour,
        DATE_ADD(mr.reservation_time, INTERVAL mr.hour HOUR) AS mr_end_time,
        mr.chairs AS mr_chairs,
        mr.table_number AS mr_table_number,
        clr.ReservationTime AS clr_reservation_time,
        clr.Duration AS clr_duration,
        DATE_ADD(clr.ReservationTime, INTERVAL clr.Duration HOUR) AS clr_end_time,
        clr.ComputerNumber AS clr_computer_number
    FROM users u
    LEFT JOIN coffeelibro_reservations cl ON u.id = cl.reserved_by
    LEFT JOIN borrowed_books br ON u.id = br.user_id
    LEFT JOIN museum_reservations mr ON u.id = mr.reserved_by
    LEFT JOIN computer_laboratory clr ON u.id = clr.UserID
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity Log</title>
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

        h1, h2 {
            font-family: 'Quiapo', sans-serif;
            font-size: 60px;
            color: #070707;
            margin: 0 0 0 150px;
            margin-top: 30px;
            margin-left: 50px;
        }

        form {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .table-container {
            display: flex; /* Use flexbox for centering */
            justify-content: center; /* Center content horizontally */
            overflow: auto;
            max-width: 95%;
            margin: 0 auto; /* Center the container itself */
            border: 1px solid black;
            margin-bottom: -3-40px;
            max-height: 100%;
        }

        table {
            width: 100%; /* Fixed width */
            border-collapse: collapse;
            margin-left: 150px;
            
        }

        th, td {
            border: 1px solid black;
            padding: 15px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f2f2f2;
        }

        tr {
            background-color: #f1f1f1;
        }

        .buttons {
            margin: 20px 20px; /* Adjusted margin */
            margin-left: auto; /* Move buttons to the right */
            display: flex; /* Use flexbox for alignment */
            justify-content: flex-end; /* Align items to the end (right side) */
            margin-top: 10px;;
        }

        .buttons input, .buttons button {
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

        .buttons input:hover, .buttons button:hover {
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

    <h2>User Activity Log</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Cafe Libro Reservation Time</th>
                <th>Cafe Libro Hours</th>
                <th>Cafe Libro End Time</th>
                <th>Cafe Libro Chairs</th>
                <th>Cafe Libro Table Number</th>
                <th>Museum Reservation Time</th>
                <th>Museum Hours</th>
                <th>Museum End Time</th>
                <th>Museum Chairs</th>
                <th>Museum Table Number</th>
                <th>Computer Lab Reservation Time</th>
                <th>Computer Lab Duration</th>
                <th>Computer Lab End Time</th>
                <th>Computer Number</th>
                <th>Borrowed Date</th>
                <th>Due Date</th>
                
            </tr>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["user_name"] . "</td>";
                    echo "<td>" . ($row["cl_reservation_time"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["cl_hour"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["cl_end_time"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["cl_chairs"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["cl_table_number"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["mr_reservation_time"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["mr_hour"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["mr_end_time"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["mr_chairs"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["mr_table_number"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["clr_reservation_time"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["clr_duration"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["clr_end_time"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["clr_computer_number"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["br_borrowed_date"] ?? 'N/A') . "</td>";
                    echo "<td>" . ($row["br_due_date"] ?? 'N/A') . "</td>";
                    
                }
            } else {
                echo "<tr><td colspan='19'>No user activity found</td></tr>";
            }
            ?>
        </table>
       
    </div>
            

    <div class="buttons">          
            <a href="admin_dashboard.html"><button>Back to Admin Dashboard</button></a>
        </div>
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

    <script src="redirect.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
