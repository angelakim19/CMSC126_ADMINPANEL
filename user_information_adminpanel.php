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
            font-size: 100px;
            color: #070707;
            margin: 0 0 0 400px;
            margin-top: 30px;
            margin-left: 190px;
        }

        .pattern4{
            position: absolute;
            left: 30px; /* Move to the right */
            top: 0; /* Adjust the vertical position */
            width: 240px; /* Adjust the width to cover the entire width of the container */
            height: auto; /* Adjust the height to cover the entire height of the container */
            z-index: -1;
        }

        .content {
            margin-left: 50px;
        }

        table {
            width: 30%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: auto; /* Adjusted to auto */
            margin-right: 20px; /* Added margin-right */
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
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

        .buttons input {
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

        .buttons input:hover {
            background-color: #45a049;
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
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

    <h1>User Information</h1>

    <img src="pattern4.png" alt="Pattern4" class="pattern4">

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
        <div class="buttons">
            <input type="submit" name="edit" value="Edit Selected User/s">
            <input type="submit" name="delete" value="Delete Selected User/s">
            <input type="button" onclick="window.location.href='admin_dashboard.html'" value="Back to Admin Dashboard">
            <!-- Trigger the function to show the form for adding a new user -->
            <input type="button" onclick="showAddUserForm()" value="Add New User/s">
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
  
    <script src="redirect.js"></script>
</body>
</html>
