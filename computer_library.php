<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .edit-fields {
            display: none; /* Hide edit fields by default */
        }
    </style>
</head>
<body>
    <h1>Admin Panel</h1>
    <table>
        <tr>
            <th>Computer Number</th>
            <th>Status</th>
            <th>Reservation Time</th>
            <th>Duration (Hours)</th>
            <th>End Time</th>
            <th>Reserved By</th>
            <th>Action</th>
        </tr>
        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'registration_db');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update computer status and reservation details
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $computer_id = $_POST['computer_id'];
            $new_status = $_POST['new_status'];

            // Reset the reservation details if the computer is marked as Available
            if ($new_status == 'Available') {
                $update_sql = "UPDATE computer_laboratory 
                               SET Status = '$new_status', 
                                   ReservationTime = NULL, 
                                   Duration = NULL, 
                                   EndTime = NULL, 
                                   UserID = NULL 
                               WHERE ComputerID = $computer_id";
            } else {
                $reservation_time = $_POST['reservation_time'];
                $duration = $_POST['duration'];
                $end_time = date('Y-m-d H:i:s', strtotime("$reservation_time + $duration hours"));
                $update_sql = "UPDATE computer_laboratory 
                               SET Status = '$new_status', 
                                   ReservationTime = '$reservation_time', 
                                   Duration = '$duration', 
                                   EndTime = '$end_time', 
                                   UserID = '" . $_POST['user_id'] . "' 
                               WHERE ComputerID = $computer_id";
            }
            
            $conn->query($update_sql);
        }

        // Fetch computers data with user information
        $sql = "SELECT c.*, CONCAT(u.LastName, ', ', u.FirstName, ' ', u.MiddleName) AS ReservedBy 
                FROM computer_laboratory c 
                LEFT JOIN users u ON c.UserID = u.id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Calculate end time dynamically
                $end_time = $row['EndTime'] ? $row['EndTime'] : ($row['ReservationTime'] ? date('Y-m-d H:i:s', strtotime($row['ReservationTime'] . ' +' . $row['Duration'] . ' hours')) : 'N/A');
                ?>
                <tr>
                    <td><?php echo $row['ComputerNumber']; ?></td>
                    <td><?php echo $row['Status']; ?></td>
                    <td><?php echo ($row['ReservationTime'] ? $row['ReservationTime'] : 'N/A'); ?></td>
                    <td><?php echo ($row['Duration'] ? $row['Duration'] : 'N/A'); ?></td>
                    <td><?php echo $end_time; ?></td>
                    <td><?php echo ($row['ReservedBy'] ? $row['ReservedBy'] : 'N/A'); ?></td>
                    <td>
                        <button onclick="toggleEditFields(<?php echo $row['ComputerID']; ?>)">Edit</button>
                        <div id="edit-fields-<?php echo $row['ComputerID']; ?>" class="edit-fields">
                            <form method='post'>
                                <input type='hidden' name='computer_id' value='<?php echo $row['ComputerID']; ?>'>
                                <input type='hidden' name='user_id' value='<?php echo ($row['UserID'] ? $row['UserID'] : ''); ?>'>
                                <select name='new_status'>
                                    <option value='Available' <?php echo ($row['Status'] == 'Available' ? 'selected' : ''); ?>>Available</option>
                                    <option value='Reserved' <?php echo ($row['Status'] == 'Reserved' ? 'selected' : ''); ?>>Reserved</option>
                                </select>
                                <br>
                                Reservation Time: <input type='datetime-local' name='reservation_time' value='<?php echo ($row['ReservationTime'] ? date('Y-m-d\TH:i', strtotime($row['ReservationTime'])) : ''); ?>' required>
                               
                                <br>
                                Duration (Hours): <input type='number' name='duration' value='<?php echo ($row['Duration'] ? $row['Duration'] : ''); ?>' required>
                                <br>
                                <input type='submit' value='Update'>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='7'>No computers found</td></tr>";
        }

        $conn->close();
        ?>

    </table>
    <div><br><button onclick="window.location.href = 'admin_dashboard.html'">Admin Dashboard</button>
    <button onclick="window.location.href = 'library_places.html'">Libraray Spaces</button></div>

    <script>
        function toggleEditFields(computerID) {
            var editFields = document.getElementById('edit-fields-' + computerID);
            if (editFields.style.display === 'none') {
                editFields.style.display = 'block';
            } else {
                editFields.style.display = 'none';
            }
        }
    </script>
</body>
</html>
