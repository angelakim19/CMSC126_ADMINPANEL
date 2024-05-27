<?php
session_start();
include 'db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if selected_ids are set in session
if (!isset($_SESSION['selected_ids'])) {
    // Redirect to borrowed_books.php if no books are selected
    header("Location: borrowed_books.php");
    exit();
}

// Retrieve selected IDs from session and escape them for SQL query
$selected_ids = $_SESSION['selected_ids'];
$escaped_ids = array_map('intval', $selected_ids);  // Ensure IDs are integers

// Fetch book information from database for the selected IDs
$sql = "SELECT borrowed_books.id, users.studentnumber, users.lastname, users.firstname, users.middlename,
               borrowed_books.book_title, borrowed_books.author, borrowed_books.isbn, 
               borrowed_books.borrowed_date, borrowed_books.due_date, borrowed_books.returned_date
        FROM borrowed_books
        JOIN users ON borrowed_books.user_id = users.id
        WHERE borrowed_books.id IN (" . implode(',', $escaped_ids) . ")";
$result = $conn->query($sql);


if ($result->num_rows > 0) {
    // Display edit form for each selected book
    while ($row = $result->fetch_assoc()) {
        ?>
        <form method="post" action="update_book.php">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($row['id']); ?>">
            <label for="studentnumber">Student Number:</label>
            <input type="text" name="studentnumber" value="<?php echo htmlspecialchars($row['studentnumber']); ?>"><br>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($row['lastname']); ?>"><br>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($row['firstname']); ?>"><br>
            <label for="middle_name">Middle Name:</label>
            <input type="text" name="middle_name" value="<?php echo htmlspecialchars($row['middlename']); ?>"><br>
            <label for="book_title">Book Title:</label>
            <input type="text" name="book_title" value="<?php echo htmlspecialchars($row['book_title']); ?>"><br>
            <label for="author">Author:</label>
            <input type="text" name="author" value="<?php echo htmlspecialchars($row['author']); ?>"><br>
            <label for="isbn">ISBN:</label>
            <input type="text" name="isbn" value="<?php echo htmlspecialchars($row['isbn']); ?>"><br>
            <label for="borrowed_date">Borrowed Date:</label>
            <input type="text" name="borrowed_date" value="<?php echo htmlspecialchars($row['borrowed_date']); ?>"><br>
            <label for="due_date">Due Date:</label>
            <input type="text" name="due_date" value="<?php echo htmlspecialchars($row['due_date']); ?>"><br>
            <input type="submit" value="Update">
        </form>
        <?php
    }
} else {
    echo "No books found.";
}

// Close connection
$conn->close();
?>
