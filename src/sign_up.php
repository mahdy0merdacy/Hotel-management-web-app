<?php
session_start(); // Start session to store user data after signup

// Include your database connection file
require_once 'db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //default user and customer
    $role ='user';
    $customerid=NULL;

    // Prepare SQL to insert the user into the database
    $sql = "INSERT INTO users (username,password_hash,role, customer_id,email) VALUES (?, ?, ?,?,?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $username, $hashed_password,$role,$customerid,$email);

        // Execute the query
        if ($stmt->execute()) {
            // Success: Redirect to login page or show success message
            $_SESSION['message'] = "User registered successfully! You can now log in.";
            header("Location: login.html"); // Redirect to login page
            exit();
        } else {
            // Error: Display error message
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
    $conn->close(); // Close the connection
}
?>
