<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'db_connection.php';

// Handle the booking process when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $check_in_date = $_POST['check_in_date'];
    $check_out_date = $_POST['check_out_date'];
    $customer_id = $_SESSION['user_id']; // Use the logged-in user's ID

    // Check if the room is available during the selected dates
    $availability_check_sql = "SELECT * FROM reservations WHERE room_id = ? 
                               AND (check_in_date BETWEEN ? AND ?) 
                               OR (check_out_date BETWEEN ? AND ?)";

    $stmt = $conn->prepare($availability_check_sql);
    $stmt->bind_param("issss", $room_id, $check_in_date, $check_out_date, $check_in_date, $check_out_date);
    $stmt->execute();
    $result = $stmt->get_result();

    // If any reservation overlaps, the room is not available
    if ($result->num_rows > 0) {
        echo "Sorry, the room is not available during the selected dates.";
    } else {
        // The room is available, now confirm the reservation
        $reservation_sql = "INSERT INTO reservations (customer_id, room_id, check_in_date, check_out_date, status) 
                            VALUES (?, ?, ?, ?, 'CONFIRMED')";
        
        $stmt = $conn->prepare($reservation_sql);
        $stmt->bind_param("iiss", $customer_id, $room_id, $check_in_date, $check_out_date);
        
        if ($stmt->execute()) {
            // After successfully confirming the reservation, update the room availability
            $update_room_availability_sql = "UPDATE rooms SET availability = FALSE WHERE room_id = ?";
            $stmt = $conn->prepare($update_room_availability_sql);
            $stmt->bind_param("i", $room_id);
            $stmt->execute();

            echo "Your reservation has been confirmed! Thank you for booking.";
        } else {
            echo "Error: Could not process the reservation.";
        }
    }
}
?>

