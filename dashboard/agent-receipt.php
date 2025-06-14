<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "agent") {
    header("Location: ../login.php");
    exit();
}
include '../includes/db.php';

$query = "
    SELECT b.booking_reference, u.fullname, d.name AS destination, d.price, b.booking_date, b.payment_method, b.payment_status
    FROM bookings b
    JOIN destinations d ON b.destination_id = d.id
    JOIN users u ON b.user_id = u.id
    ORDER BY b.booking_date DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agent - Receipts</title>
</head>
<body>
<h1 style="text-align:center;">📑 Agent Receipts</h1>
<table border="1" style="margin:auto;">
    <tr>
        <th>Reference</th>
        <th>Tourist</th>
        <th>Destination</th>
        <th>Price</th>
        <th>Date</th>
        <th>Payment Method</th>
        <th>Payment Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['booking_reference']; ?></td>
        <td><?= $row['fullname']; ?></td>
        <td><?= $row['destination']; ?></td>
        <td>₱<?= number_format($row['price'], 2); ?></td>
        <td><?= $row['booking_date']; ?></td>
        <td><?= $row['payment_method']; ?></td>
        <td><?= $row['payment_status']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
