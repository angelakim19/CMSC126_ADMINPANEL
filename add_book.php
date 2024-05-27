<!DOCTYPE html>
<html>
<head>
    <title>Add Book Borrowed</title>
</head>
<body>
    <h2>Add Book Borrowed</h2>
    <?php
    session_start();
    include 'db_connection.php';

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Retrieve form data
      $book_title = $_POST['book_title'];
      $author = $_POST['author'];
      $isbn = $_POST['isbn'];
      $borrowed_date = $_POST['borrowed_date'];
      $due_date = $_POST['due_date'];

      // Insert book information into the database
      $sql = "INSERT INTO borrowed_books (book_title, author, isbn, borrowed_date, due_date) 
              VALUES ('$book_title', '$author', '$isbn', '$borrowed_date', '$due_date')";

      if ($conn->query($sql) === TRUE) {
          // Alert success message
          echo "<script>alert('Book added successfully');</script>";

          // Redirect to borrowed_books.php after successful insertion
          echo "<script>window.location.href = 'borrowed_books.php';</script>";
          exit();
      } else {
          // Handle errors
          echo "Error adding book: " . $conn->error;
      }
  }
  ?>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <label for="book_title">Book Title:</label>
      <input type="text" name="book_title" required><br>
      <label for="author">Author:</label>
      <input type="text" name="author" required><br>
      <label for="isbn">ISBN:</label>
      <input type="text" name="isbn" required><br>
      <label for="borrowed_date">Borrowed Date:</label>
      <input type="date" name="borrowed_date" required><br>
      <label for="due_date">Due Date:</label>
      <input type="date" name="due_date" required><br>
      <input type="submit" value="Add Book">
    </form>
    <button onclick="location.href='borrowedbook&reservation.html'">Back to borrowed book</button>
</body>
</html>
