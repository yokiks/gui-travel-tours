<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "agent") {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

// Get all bookings with tourist and destination info
$query = "
    SELECT b.id, b.booking_reference, d.name AS destination_name, d.price, b.booking_date, b.status, b.payment_method, b.payment_status, u.fullname AS tourist_name
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
    <title>Agent Dashboard - All Bookings</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        a.logout {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            color: red;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ccc;
        }
        th {
            background-color: #0077cc;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .status {
            font-weight: bold;
        }
        .status.Approved {
            color: green;
        }
        .status.Cancelled,
        .status.Cancelled\ by\ Admin,
        .status.Cancelled\ by\ Tourist {
            color: red;
        }
        .status.Pending {
            color: orange;
        }
    </style>
</head>
<body>

    <h1>Welcome Agent: <?php echo $_SESSION["user"]["fullname"]; ?></h1>
    <a href="../logout.php" class="logout">Logout</a>
    <a href="agent-receipt.php" style="display:block;text-align:center;margin-top:20px;">🧾 View Receipts</a>

    <h2 style="text-align:center;">All Tourist Bookings</h2>
    
    <table>
        <tr>
            <th>Booking Reference</th>
            <th>Tourist Name</th>
            <th>Destination</th>
            <th>Price</th>
            <th>Booking Date</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['booking_reference']; ?></td>
                    <td><?= $row['tourist_name']; ?></td>
                    <td><?= $row['destination_name']; ?></td>
                    <td>₱<?= number_format($row['price'], 2); ?></td>
                    <td><?= $row['booking_date']; ?></td>
                    <td><?= $row['status']; ?></td>
                    <td><?= $row['payment_method']; ?></td>
                    <td><?= $row['payment_status']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No bookings found.</td></tr>
        <?php endif; ?>
    </table>

</body>
</html>