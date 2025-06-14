<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "tourist") {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking_id"])) {
    $bookingId = $_POST["booking_id"];

    $stmt = $conn->prepare("UPDATE bookings SET payment_status = 'Paid' WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $bookingId);
        if ($stmt->execute()) {
            header("Location: tourist.php?paid=1");
            exit();
        } else {
            echo "Failed to update payment.";
        }
    } else {
        echo "SQL Error: " . $conn->error;
    }
} else {
    header("Location: tourist.php");
    exit();
}
?>
