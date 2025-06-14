<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$result = $conn->query("
    SELECT b.booking_reference, u.fullname, d.name AS destination, b.booking_date, b.status, b.payment_method, b.payment_status
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN destinations d ON b.destination_id = d.id
    ORDER BY b.booking_date DESC
");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=admin_report.csv");

echo "Reference,Fullname,Destination,Date,Status,Payment Method,Payment Status\n";
while ($row = $result->fetch_assoc()) {
    echo "{$row['booking_reference']},{$row['fullname']},{$row['destination']},{$row['booking_date']},{$row['status']},{$row['payment_method']},{$row['payment_status']}\n";
}
exit();
?>
