<?php ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); // To start the session for the user

// Include database connection
require_once 'db_connect.php'; // You can keep your db connection logic in a separate file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input from form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query to check if email exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Check if password is correct
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect to dashboard or home page
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>
