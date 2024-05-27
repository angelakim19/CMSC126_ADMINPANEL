<?php
session_start();
include 'db_connection.php';  // Include the database connection script

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        // Directly compare the plain text password
        if ($password == $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin_dashboard.html");
            exit();  // Ensure no further code is executed after redirection
        } else {
            echo "Invalid password";
        }
    } else {
        echo "Invalid email";
    }

    $conn->close();  // Close the database connection
}
?>
