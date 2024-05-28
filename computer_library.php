<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>
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
            font-size: 75px;
            color: #070707;
            margin: 0 0 0 400px;
            margin-top: 30px;
            margin-left: 70px;
        }

        table {
            width: 95%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: auto; /* Set left margin to auto */
            margin-right: auto; /* Set right margin to auto */
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        tr {
            background-color: #f9f9f9;
        }

        .edit-fields {
            display: none; /* Hide edit fields by default */
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

    <h2>Admin Panel</h2>
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
    <div class="buttons">
        <button onclick="window.location.href = 'admin_dashboard.html'">Admin Dashboard</button>
        <button onclick="window.location.href = 'library_places.html'">Libraray Spaces</button>
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
