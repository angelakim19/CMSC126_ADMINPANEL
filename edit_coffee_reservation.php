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

// Fetch reservation details for the given reservation_id
if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];
    $sql = "SELECT * FROM coffeelibro_reservations WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission to update reservation details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reservation_id = $_POST['reservation_id'];
    $reserved_by = $_POST['reserved_by'];
    $reservation_time = $_POST['reservation_time'];
    $hour = $_POST['hour'];
    $end_time = date('Y-m-d H:i:s', strtotime($reservation_time) + ($hour * 3600));
    $chairs = $_POST['chairs'];
    $table_number = $_POST['table_number'];

    $sql = "UPDATE coffeelibro_reservations SET reserved_by = ?, reservation_time = ?, hour = ?, end_time = ?, chairs = ?, table_number = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isissii", $reserved_by, $reservation_time, $hour, $end_time, $chairs, $table_number, $reservation_id);

    if ($stmt->execute()) {
        // Redirect back to cafe_libro.php
        header("Location: cafe_libro.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch users from the database
$sql_users = "SELECT id, CONCAT(lastname, ', ', firstname) AS fullname FROM users";
$result_users = $conn->query($sql_users);

// Check if users are fetched successfully
if ($result_users->num_rows > 0) {
    // Store users in an array
    $users = [];
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "No users found";
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Coffee Reservation</title>
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
    <h2>Edit Coffee Reservation</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">

        <label for="reserved_by">Reserved By:</label>
        <select id="reserved_by" name="reserved_by" required>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['id']; ?>" <?php if ($user['id'] == $reservation['reserved_by']) echo 'selected'; ?>>
                    <?php echo $user['fullname']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="reservation_time">Reservation Time:</label>
        <input type="datetime-local" id="reservation_time" name="reservation_time" value="<?php echo date('Y-m-d\TH:i', strtotime($reservation['reservation_time'])); ?>" required><br><br>

        <label for="hour">Hour:</label>
        <input type="number" id="hour" name="hour" value="<?php echo $reservation['hour']; ?>" required><br><br>

        <label for="chairs">Chairs:</label>
        <input type="number" id="chairs" name="chairs" min="1" value="<?php echo $reservation['chairs']; ?>" required><br><br>

        <!-- Dynamic table number selection -->
        <label for="table_number">Table Number:</label>
        <select id="table_number" name="table_number" required>
            <option value="1" <?php if ($reservation['table_number'] == 1) echo 'selected'; ?>>Table 1</option>
            <option value="2" <?php if ($reservation['table_number'] == 2) echo 'selected'; ?>>Table 2</option>
            <option value="3" <?php if ($reservation['table_number'] == 3) echo 'selected'; ?>>Table 3</option>
        </select>

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
