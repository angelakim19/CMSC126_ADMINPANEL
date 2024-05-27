<?php
session_start();
include 'db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $book_id = $_POST['book_id'];
    $studentnumber = $_POST['studentnumber'];
    // Retrieve user information
    $last_name = $_POST['last_name']; // Make sure the name attribute matches in your HTML form
    $first_name = $_POST['first_name']; // Make sure the name attribute matches in your HTML form
    $middle_name = $_POST['middle_name']; // Make sure the name attribute matches in your HTML form
    $book_title = $_POST['book_title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $borrowed_date = $_POST['borrowed_date'];
    $due_date = $_POST['due_date'];

    // Update book information in the database
    $sql = "UPDATE borrowed_books AS bb
            INNER JOIN users AS u ON bb.user_id = u.id
            SET bb.book_title = '$book_title',
                bb.author = '$author',
                bb.isbn = '$isbn',
                bb.borrowed_date = '$borrowed_date',
                bb.due_date = '$due_date',
                u.studentnumber = '$studentnumber',
                u.lastname = '$last_name',  -- Check if the column name is correct
                u.firstname = '$first_name', -- Check if the column name is correct
                u.middlename = '$middle_name' -- Check if the column name is correct
            WHERE bb.id = '$book_id'";

    // Execute the query and handle errors
    if ($conn->query($sql) === TRUE) {
        // Redirect to borrowed_books.php after successful update
        header("Location: borrowed_books.php");
        exit();
    } else {
        // Handle errors
        echo "Error updating record: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>
