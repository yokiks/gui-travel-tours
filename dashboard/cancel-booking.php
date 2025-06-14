<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking_id"])) {
    $bookingId = intval($_POST["booking_id"]);
    $role = $_SESSION["user"]["role"];

    // Decide cancellation reason/status based on role
    if ($role === "admin") {
        $newStatus = "Cancelled by Admin";
        $redirect = "admin.php?cancelled=1";
    } elseif ($role === "tourist") {
        $newStatus = "Cancelled by Tourist";
        $redirect = "tourist.php?cancelled=1";
    } else {
        // Unauthorized role
        header("Location: ../login.php");
        exit();
    }

    // Update the booking status
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $newStatus, $bookingId);
        if ($stmt->execute()) {
            header("Location: $redirect");
            exit();
        } else {
            echo "<p style='text-align:center;color:red;'>❌ Failed to cancel booking.</p>";
        }
    } else {
        echo "<p style='text-align:center;color:red;'>SQL Error: " . $conn->error . "</p>";
    }
} else {
    // Direct access fallback
    if ($_SESSION["user"]["role"] === "admin") {
        header("Location: admin.php");
    } elseif ($_SESSION["user"]["role"] === "tourist") {
        header("Location: tourist.php");
    } else {
        header("Location: ../login.php");
    }
    exit();
}
?>
