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
    LEFT JOIN book_reservations br ON u.id = br.user_id
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
        th, td {
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>User Activity Log</h2>
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
            <th>Borrowed Status</th>
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
                echo "<td>" . ($row["borrowed_status"] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='19'>No user activity found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
