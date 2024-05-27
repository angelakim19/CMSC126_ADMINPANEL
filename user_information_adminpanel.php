<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connection.php';

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
    <script>
        function showAddUserForm() {
            // Create a form for user input
            var form = document.createElement('form');
            form.setAttribute('action', 'add_user.php');
            form.setAttribute('method', 'post');

            // Create input fields for user information
            var inputs = ['Lastname', 'Firstname', 'Middlename', 'Student Number', 'Email', 'Department/College', 'Program', 'Position', 'Phone Number', 'Password'];
            inputs.forEach(function(label) {
                var input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('name', label.toLowerCase().replace(/ /g, ''));
                input.setAttribute('placeholder', label);
                form.appendChild(input);
                form.appendChild(document.createElement('br'));
            });

            // Create submit button
            var submitButton = document.createElement('input');
            submitButton.setAttribute('type', 'submit');
            submitButton.setAttribute('value', 'Add User');
            form.appendChild(submitButton);

            // Append form to the body and submit it
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <h1>User Information</h1>

    <form action="perform_actions.php" method="post">
        <table border="1">
            <tr>
                <th>Select</th>
                <th>ID</th>
                <th>Lastname</th>
                <th>Firstname</th>
                <th>Middlename</th>
                <th>Student Number</th>
                <th>Email</th>
                <th>Department/College</th>
                <th>Program</th>
                <th>Position</th>
                <th>Phone Number</th>
                <th>Password</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><input type="checkbox" name="selected_users[]" value="<?php echo $row['id']; ?>"></td>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['lastname']; ?></td>
                <td><?php echo $row['firstname']; ?></td>
                <td><?php echo $row['middlename']; ?></td>
                <td><?php echo $row['studentnumber']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['college']; ?></td>
                <td><?php echo $row['program']; ?></td>
                <td><?php echo $row['position']; ?></td>
                <td><?php echo $row['phonenumber']; ?></td>
                <td><?php echo $row['password']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <input type="submit" name="edit" value="Edit Selected User/s">
        <input type="submit" name="delete" value="Delete Selected User/s">
        <input type="button" onclick="window.location.href='admin_dashboard.html'" value="Back to Admin Dashboard">

        <!-- Trigger the function to show the form for adding a new user -->
        <input type="button" onclick="showAddUserForm()" value="Add New User/s">
    </form>
</body>
</html>
