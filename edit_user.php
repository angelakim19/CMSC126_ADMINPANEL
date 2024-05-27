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
</head>
<body>
    <h1>Edit User Information</h1>
    <form method="post" action="edit_user.php">
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
        <input type="submit" value="Update Selected Users">
    </form>

    <button onclick="goBackone()">Back to Admin Dashboard</button>
    <button onclick="goBacktwo()">Back to User Information</button>
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
$conn->close();
?>
