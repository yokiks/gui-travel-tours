<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "tourist") {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';
$userId = $_SESSION["user"]["id"];

$query = "
    SELECT b.booking_reference, d.name AS destination, d.price, b.booking_date, b.status, b.payment_method, b.payment_status
    FROM bookings b
    JOIN destinations d ON b.destination_id = d.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tourist Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .actions {
            text-align: center;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background: #0077cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #005fa3;
        }
        table {
            margin: auto;
            width: 90%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #333;
            color: white;
        }
        .status.Approved { color: green; }
        .status.Cancelled, .status['Cancelled by Admin'], .status['Cancelled by Tourist'] { color: red; }
        .status.Pending { color: orange; }
    </style>
</head>
<body>

<h1>Your Booking Receipt(s)</h1>

<div class="actions">
    <button onclick="window.print()">🖨️ Print Receipt</button>
</div>

<div id="receipt-content">
<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Reference</th>
            <th>Destination</th>
            <th>Price</th>
            <th>Booking Date</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['booking_reference']; ?></td>
                <td><?= $row['destination']; ?></td>
                <td>₱<?= number_format($row['price'], 2); ?></td>
                <td><?= $row['booking_date']; ?></td>
                <td class="status <?= $row['status']; ?>"><?= $row['status']; ?></td>
                <td><?= $row['payment_method']; ?></td>
                <td><?= $row['payment_status']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center;">❌ You have no bookings yet.</p>
<?php endif; ?>
</div>

</body>
</html>
