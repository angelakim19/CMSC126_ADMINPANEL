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
            font-size: 60px;
            color: #070707;
            margin: 0 0 0 150px;
            margin-top: 30px;
            margin-left: 50px;
        }

        form {
            display: block;
            margin-top: 0em;
            unicode-bidi: isolate;
            width: 80%;
        }
        
        .table-container {
            font-family: 'OpenSans', sans-serif;
            display: flex; /* Use flexbox for centering */
            justify-content: center; /* Center content horizontally */
            width: 50%;
            margin: 30px auto; /* Center the container itself */
            border: 2px solid black;
            padding: 30px;
            border-radius: 30px;
            background-color: rgba(54, 81, 54, 0.259);
            box-shadow: 0 4px 8px rgba(0.8, 0.8, 0.8, 0.8);
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            flex: 1;
            text-align: left;
            margin-right: 5px;
            color:white
        }

        .form-group input {
            flex: 2;
            width: calc(100% - 8px); /* Adjust width to account for padding */
            padding: 8px; /* Increase padding */
        }

        .buttons {
            margin: 20px 20px; /* Adjusted margin */
            margin-left: auto; /* Move buttons to the right */
            display: flex; /* Use flexbox for alignment */
            justify-content: center; 
        }

        .buttons input, .buttons button {
            padding: 10px 20px;
            margin-left: 10px;
            font-size: 16px;
            border: 2px solid black;
            border-radius: 5px;
            background-color: #535151;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }

        .buttons input:hover, .buttons button:hover {
            background-color: #45a049;
            transform: scale(1.05);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
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

    <h1>Add New User</h1>

    <div class="table-container">
        <form action="" method="post">
            <div class="form-group">
                <label for="lastname">Lastname:</label>
                <input type="text" id="lastname" name="lastname">
            </div>
            <div class="form-group">
                <label for="firstname">Firstname:</label>
                <input type="text" id="firstname" name="firstname">
            </div>
            <div class="form-group">
                <label for="middlename">Middlename:</label>
                <input type="text" id="middlename" name="middlename">
            </div>
            <div class="form-group">
                <label for="studentnumber">Student Number:</label>
                <input type="text" id="studentnumber" name="studentnumber">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="college">Department/College:</label>
                <input type="text" id="college" name="college">
            </div>
            <div class="form-group">
                <label for="program">Program:</label>
                <input type="text" id="program" name="program">
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position">
            </div>
            <div class="form-group">
                <label for="phonenumber">Phone Number:</label>
                <input type="text" id="phonenumber" name="phonenumber">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="buttons">
                <input type="submit" name="add" value="Add New User">
                <!-- Button to go back to Admin Dashboard -->
                <button type="button" onclick="window.location.href='admin_dashboard.html'">Back to Admin Dashboard</button>

                <!-- Button to go back to User Information -->
                <button type="button" onclick="window.location.href='user_information_adminpanel.php'">Back to User Information</button>
            </div>
        </form>
    </div>
    
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
</body>
</html>
