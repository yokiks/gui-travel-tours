<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking_id"])) {
    $bookingId = $_POST["booking_id"];

    // Check if booking is paid first
    $check = $conn->prepare("SELECT payment_status FROM bookings WHERE id = ?");
    $check->bind_param("i", $bookingId);
    $check->execute();
    $result = $check->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['payment_status'] === 'Paid') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'Approved' WHERE id = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        header("Location: admin.php?approved=1");
    } else {
        header("Location: admin.php?error=notpaid");
    }
    exit();
}
?>
