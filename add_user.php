<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    // Retrieve form data
    $lastname = $_POST["lastname"];
    $firstname = $_POST["firstname"];
    $middlename = $_POST["middlename"];
    $studentnumber = $_POST["studentnumber"];
    $email = $_POST["email"];
    $college = $_POST["college"];
    $program = $_POST["program"];
    $position = $_POST["position"];
    $phonenumber = $_POST["phonenumber"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Insert new user into database
    $sql = "INSERT INTO users (lastname, firstname, middlename, studentnumber, email, college, program, position, phonenumber, password) 
            VALUES ('$lastname', '$firstname', '$middlename', '$studentnumber', '$email', '$college', '$program', '$position', '$phonenumber', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New user added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
</head>
<body>
    <h1>Add New User</h1>

    <form action="" method="post">
        <label>Lastname:</label><br>
        <input type="text" name="lastname"><br>
        <label>Firstname:</label><br>
        <input type="text" name="firstname"><br>
        <label>Middlename:</label><br>
        <input type="text" name="middlename"><br>
        <label>Student Number:</label><br>
        <input type="text" name="studentnumber"><br>
        <label>Email:</label><br>
        <input type="email" name="email"><br>
        <label>Department/College:</label><br>
        <input type="text" name="college"><br>
        <label>Program:</label><br>
        <input type="text" name="program"><br>
        <label>Position:</label><br>
        <input type="text" name="position"><br>
        <label>Phone Number:</label><br>
        <input type="text" name="phonenumber"><br>
        <label>Password:</label><br>
        <input type="password" name="password"><br><br>
        <input type="submit" name="add" value="Add New User">
    </form>

    <!-- Button to go back to Admin Dashboard -->
    <button onclick="window.location.href='admin_dashboard.html'">Back to Admin Dashboard</button>

    <!-- Button to go back to User Information -->
    <button onclick="window.location.href='user_information_adminpanel.php'">Back to User Information</button>
</body>
</html>
