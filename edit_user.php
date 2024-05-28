<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_users'])) {
    $selected_users = $_POST['selected_users'];
    $success_count = 0; // Counter for successful updates
    foreach ($selected_users as $user_id) {
        // Retrieve user information from the database
        $sql = "SELECT * FROM users WHERE id='$user_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Process each selected user for editing
            $lastname = $_POST['lastname_' . $user_id];
            $firstname = $_POST['firstname_' . $user_id];
            $middlename = $_POST['middlename_' . $user_id];
            $student_number = $_POST['studentnumber_' . $user_id];
            $email = $_POST['email_' . $user_id];
            $college = $_POST['college_' . $user_id];
            $program = $_POST['program_' . $user_id];
            $position = $_POST['position_' . $user_id];
            $phonenumber = $_POST['phonenumber_' . $user_id];
            $password = $_POST['password_' . $user_id];

            // Update user information in the database
            $sql = "UPDATE users SET 
                  lastname='$lastname', 
                  firstname='$firstname', 
                  middlename='$middlename', 
                  studentnumber='$student_number', 
                  email='$email', 
                  college='$college', 
                  program='$program', 
                  position='$position', 
                  phonenumber='$phonenumber', 
                  password='$password' 
              WHERE id='$user_id'";
            if ($conn->query($sql) === TRUE) {
                // Increment the success counter
                $success_count++;
            } else {
                echo "Error updating user with ID $user_id: " . $conn->error . "<br>";
            }
        }
    }
    // Check if any updates were successful
    if ($success_count > 0) {
        echo '<script>alert("Users updated successfully");</script>';
    }
}

// Retrieve all users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Users</title>
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
            margin-bottom: auto;
        }

        .table-container {
            display: flex; /* Use flexbox for centering */
            justify-content: center; /* Center content horizontally */
            overflow: auto;
            max-width: 95%;
            margin: 0 auto; /* Center the container itself */
            border: 1px solid black;
        }

        table {
            width: 1200px; /* Fixed width */
            border-collapse: collapse;
            margin-left: 500px;
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
    <h1>Edit User Information</h1>
    <form method="post" action="edit_user.php">
        <div class="table-container">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Student Number</th>
                    <th>Email</th>
                    <th>College</th>
                    <th>Program</th>
                    <th>Position</th>
                    <th>Phone Number</th>
                    <th>Password</th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_users[]" value="<?php echo $row['id']; ?>"></td>
                        <td><input type="text" name="lastname_<?php echo $row['id']; ?>" value="<?php echo $row['lastname']; ?>"></td>
                        <td><input type="text" name="firstname_<?php echo $row['id']; ?>" value="<?php echo $row['firstname']; ?>"></td>
                        <td><input type="text" name="middlename_<?php echo $row['id']; ?>" value="<?php echo $row['middlename']; ?>"></td>
                        <td><input type="text" name="studentnumber_<?php echo $row['id']; ?>" value="<?php echo $row['studentnumber']; ?>"></td>
                        <td><input type="email" name="email_<?php echo $row['id']; ?>" value="<?php echo $row['email']; ?>"></td>
                        <td><input type="text" name="college_<?php echo $row['id']; ?>" value="<?php echo $row['college']; ?>"></td>
                        <td><input type="text" name="program_<?php echo $row['id']; ?>" value="<?php echo $row['program']; ?>"></td>
                        <td><input type="text" name="position_<?php echo $row['id']; ?>" value="<?php echo $row['position']; ?>"></td>
                        <td><input type="text" name="phonenumber_<?php echo $row['id']; ?>" value="<?php echo $row['phonenumber']; ?>"></td>
                        <td><input type="password" name="password_<?php echo $row['id']; ?>" value="<?php echo $row['password']; ?>"></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <div class="buttons">
            <input type="submit" value="Update Selected Users">
            <button onclick="goBackone()">Back to Admin Dashboard</button>
            <button onclick="goBacktwo()">Back to User Information</button>
        </div>
    </form>
    
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

<?php
$conn->close();
?>
