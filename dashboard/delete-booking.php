<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: ../dashboard/admin.php");
    exit();
}

include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking_id"])) {
    $bookingId = $_POST["booking_id"];

    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $bookingId);

    if ($stmt->execute()) {
        header("Location: admin.php?deleted=1");
        exit();
    } else {
        echo "Failed to delete booking.";
    }
} else {
    header("Location: admin.php");
    exit();
}
?>
