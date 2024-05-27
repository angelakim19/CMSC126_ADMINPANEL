<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connection.php';

$sql = "SELECT borrowed_books.id, users.lastname, users.firstname, users.middlename, users.studentnumber,
               borrowed_books.book_title, borrowed_books.author, borrowed_books.isbn, 
               borrowed_books.borrowed_date, borrowed_books.due_date, borrowed_books.returned_date
        FROM borrowed_books
        JOIN users ON borrowed_books.user_id = users.id";
$result = $conn->query($sql);
?>

<h2>Borrowed Books</h2>
<form id="borrowedBooksForm" action="process_books.php" method="post">
    <table border="1">
        <tr>
            <th>Select</th>
            <th>Book ID</th>
            <th>Student Number</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Book Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Borrowed Date</th>
            <th>Due Date</th>
            <th>Returned Date</th>
            <th>Status</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><input type="checkbox" name="selected_ids[]" value="<?php echo $row['id']; ?>"></td>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['studentnumber']; ?></td>
            <td><?php echo $row['lastname']; ?></td>
            <td><?php echo $row['firstname']; ?></td>
            <td><?php echo $row['middlename']; ?></td>
            <td><?php echo $row['book_title']; ?></td>
            <td><?php echo $row['author']; ?></td>
            <td><?php echo $row['isbn']; ?></td>
            <td><?php echo $row['borrowed_date']; ?></td>
            <td><?php echo $row['due_date']; ?></td>
            <td><?php echo $row['returned_date']; ?></td>
            <td>
                <?php 
                    if (!empty($row['returned_date'])) {
                        echo "Returned";
                    } elseif (date("Y-m-d") > $row['due_date']) {
                        echo "Overdue";
                    } else {
                        echo "Borrowed";
                    }
                ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <button type="submit" name="action" value="edit">Edit Selected</button>
   
    <button type="submit" name="action" value="delete">Delete Selected</button>
</form>

    <button onclick="location.href='add_book.php'">Add Book Borrowed</button>
</form>
<button onclick="location.href='admin_dashboard.html'">Back to Admin Dashboard</button>

<?php
$conn->close();
?>
