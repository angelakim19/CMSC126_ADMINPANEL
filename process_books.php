<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the action is set
    if(isset($_POST['action'])) {
        // Handle different actions
        switch($_POST['action']) {
            case 'edit':
                // Handle edit operation
                if(isset($_POST['selected_ids'])) {
                    // Store the selected IDs in a session variable to access in the edit page
                    $_SESSION['selected_ids'] = $_POST['selected_ids'];
                    // Redirect to the edit page
                    header("Location: edit_books.php");
                    exit();
                }
                break;
                case 'delete':
                    // Handle delete operation
                    if(isset($_POST['selected_ids'])) {
                        foreach($_POST['selected_ids'] as $selected_id) {
                            // Perform delete operation for book with ID $selected_id
                            $sql = "DELETE FROM borrowed_books WHERE id = $selected_id"; // Assuming your primary key column is 'id'
                            // Execute the SQL query
                            if ($conn->query($sql) !== TRUE) {
                                // Handle errors here if necessary
                                echo "Error deleting record: " . $conn->error;
                            }
                        }
                    }
                    break;
                
            case 'add':
                // Handle add operation
                // Retrieve form data for adding a new book
                $book_title = $_POST['book_title'];
                $author = $_POST['author'];
                $isbn = $_POST['isbn'];
                $borrowed_date = $_POST['borrowed_date'];
                $due_date = $_POST['due_date'];
                // Perform validation on input data if necessary

                // Perform the insert operation to add a new book
                // Example: $sql = "INSERT INTO books (book_title, author, isbn, borrowed_date, due_date) VALUES ('$book_title', '$author', '$isbn', '$borrowed_date', '$due_date')";
                break;
            default:
                // Handle invalid action
                break;
        }
    }
}

// After handling the operations, you can redirect the user to a different page
header("Location: borrowed_books.php");
exit();

$conn->close();
?>
