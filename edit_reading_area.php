<?php
// Include database connection
include 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission
    $reservation_id = $_POST['reservation_id'];
    $reserved_by = $_POST['reserved_by'];
    $reservation_time = $_POST['reservation_time'];
    $table_number = $_POST['table_number'];
    $chairs = $_POST['chairs'];
    $hours = $_POST['hour'];

    // Update the reservation details in the database
    $sql = "UPDATE reading_area SET reserved_by = ?, reservation_time = ?, table_number = ?, chairs = ?, hour = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("isiisi", $reserved_by, $reservation_time, $table_number, $chairs, $hours, $reservation_id);

    if ($stmt->execute()) {
        // Redirect to reading_area.php after successful update
        header("Location: reading_area.php");
        exit();
    } else {
        echo "Error updating reservation: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Fetch the reservation details for the given reservation ID
    if (!isset($_GET['reservation_id'])) {
        die("Reservation ID is required.");
    }
    
    $reservation_id = $_GET['reservation_id'];
    $sql = "SELECT * FROM reading_area WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No reservation found with ID $reservation_id";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .photo-icons {
            display: flex;
            padding: 10px;
            cursor: pointer;
            border-radius: 50px;
        }
    
        .photo-icon {
            height: 40px;
            width: 40px;
            margin-right: 18px;
        }

        h2 {
            font-family: 'Quiapo', sans-serif;
            font-size: 45px;
            color: #f8f1f1;
            margin-top: 30px;
            text-align: center;
        }

        .container {
            width: 90%; /* Use a percentage width for responsiveness */
            max-width: 400px; /* Set a max-width to maintain a reasonable size */
            background-color: rgba(72, 74, 72, 0.522);
            margin: 50px auto; /* Center the container horizontally and add top margin */
            padding: 20px;
            border-radius: 10px;
            color: aliceblue;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            box-sizing: border-box; /* Ensure padding and border are included in width */
        }

        form {
            display: flex;
            flex-direction: column; /* Stack form elements vertically */
        }

        label {
            margin-top: 10px;
        }

        input, select {
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box; /* Ensure padding and border are included in width */
            width: 100%; /* Make inputs full width */
        }

        input[type="submit"] {
            background-color: #535151;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            margin-top: 20px;
        }

        input[type="submit"]:hover {
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

    <div class="container">
    <h2>Edit Reservation</h2>
        <form action="edit_reading_area.php" method="post">
            <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($row['reservation_id']); ?>">
            
            <label for="reserved_by">Reserved By:</label>
            <select name="reserved_by">
                <?php
                // Fetch user IDs and names from the users table
                $sql_users = "SELECT id, CONCAT(lastname, ', ', firstname, ' ', middlename) AS fullname FROM users";
                $result_users = $conn->query($sql_users);

                if ($result_users->num_rows > 0) {
                    while($row_user = $result_users->fetch_assoc()) {
                        // Option value is the user ID, and the displayed text is the user's full name
                        echo "<option value='" . $row_user['id'] . "'";
                        if ($row['reserved_by'] == $row_user['id']) {
                            echo " selected"; // If this user is the one who made the reservation, select this option
                        }
                        echo ">" . htmlspecialchars($row_user['fullname']) . "</option>";
                    }
                }
                ?>
            </select><br>
            
            <label for="reservation_time">Reservation Time:</label>
            <input type="datetime-local" name="reservation_time" value="<?php echo date('Y-m-d\TH:i', strtotime($row['reservation_time'])); ?>"><br>
            
            <label for="table_number">Table Number:</label>
            <input type="number" name="table_number" value="<?php echo htmlspecialchars($row['table_number']); ?>"><br>
            
            <label for="chairs">Chairs:</label>
            <input type="number" name="chairs" value="<?php echo htmlspecialchars($row['chairs']); ?>"><br>
            
            <label for="hour">Hours:</label>
            <input type="number" name="hour" value="<?php echo htmlspecialchars($row['hour']); ?>"><br>
            
            <input type="submit" value="Update Reservation">
        </form>
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
</body>
</html>

<?php
// Close connection
$conn->close();
?>
