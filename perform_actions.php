<?php
// Check if any action button is clicked
if(isset($_POST['edit'])) {
    // Redirect to the edit page with selected user IDs
    $selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : [];
    if(!empty($selected_users)) {
        $ids = implode(',', $selected_users);
        header("Location: edit_user.php?ids=$ids");
        exit();
    }
} elseif(isset($_POST['delete'])) {
    // Perform delete action for selected users
    include 'db_connection.php'; // Include database connection
    
    $selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : [];
    if(!empty($selected_users)) {
        $ids = implode(',', $selected_users);
        $sql = "DELETE FROM users WHERE id IN ($ids)";
        if ($conn->query($sql) === TRUE) {
            // Deletion successful
            header("Location: user_information_adminpanel.php");
            exit();
        } else {
            // Error occurred
            echo "Error deleting record: " . $conn->error;
        }
        $conn->close();
    }
} elseif(isset($_POST['reset'])) {
    // Perform reset action for selected users
    include 'db_connection.php'; // Include database connection
    
    $selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : [];
    if(!empty($selected_users)) {
        foreach($selected_users as $user_id) {
            // Generate a new random password
            $new_password = generateRandomPassword(); // Function to generate a random password
            
            // Update the user's password in the database
            $sql = "UPDATE users SET password='$new_password' WHERE id='$user_id'";
            if ($conn->query($sql) !== TRUE) {
                // Error occurred
                echo "Error updating record: " . $conn->error;
            }
        }
        // Redirect back to user information page
        header("Location: user_information_adminpanel.php");
        exit();
    }
}

// Function to generate a random password
function generateRandomPassword($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $length);
}
?>
