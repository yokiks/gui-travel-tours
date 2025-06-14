<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

// Get all bookings with user and destination info
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter = $search ? "WHERE u.fullname LIKE '%$search%' OR b.booking_reference LIKE '%$search%'" : '';

$bookings = $conn->query("
    SELECT b.id, b.booking_reference, d.name AS destination_name, d.price, b.booking_date, b.status, b.payment_method, b.payment_status, u.fullname
    FROM bookings b
    JOIN destinations d ON b.destination_id = d.id
    JOIN users u ON b.user_id = u.id
    $filter
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Bookings</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        table {
            width: 90%;
            margin: 40px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #0077cc;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
    </style>
</head>
<?php if (isset($_GET['deleted'])): ?>
    <p style="text-align:center; color:green;">Booking deleted successfully!</p>
<?php endif; ?>
<body>
    <h1 style="text-align: center;">Welcome Admin: <?php echo $_SESSION["user"]["fullname"]; ?></h1>
    <a href="../logout.php" style="display:block;text-align:center;margin-bottom:20px;">Logout</a>
    <a href="admin-report.php" style="text-align:center;display:block;margin:20px;">📄 Download Report (CSV)</a>

    <h2 style="text-align:center;">All Bookings</h2>
    <form method="GET" style="text-align: center; margin: 20px 0;">
    <input type="text" name="search" placeholder="Search by tourist or reference" style="padding: 8px; width: 300px;">
    <button type="submit" style="padding: 8px 16px;">Search</button>
    </form>
    <table>
        <tr>
            <th>Booking Reference</th>
            <th>Tourist</th>
            <th>Destination</th>
            <th>Price</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
            <th>Payment Method</th>
            <th>Payment Status</th>
        </tr>
<?php while($row = $bookings->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['booking_reference']; ?></td>
        <td><?php echo $row['fullname']; ?></td>
        <td><?php echo $row['destination_name']; ?></td>
        <td>₱<?php echo number_format($row['price'], 2); ?></td>
        <td><?php echo $row['booking_date']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['payment_method']; ?></td>
        <td><?php echo $row['payment_status']; ?></td>
        <td>
            <form action="approve-booking.php" method="POST" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                <button type="submit">Approve</button>
            </form>
            <form action="delete-booking.php" method="POST" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
            <form action="cancel-booking.php" method="POST" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
    </table>
</body>
<?php if (isset($_GET["approved"])): ?>
    <p style="color: green; text-align: center;">✅ Booking approved successfully!</p>
<?php endif; ?>

<?php if (isset($_GET["deleted"])): ?>
    <p style="color: red; text-align: center;">🗑 Booking deleted successfully!</p>
<?php endif; ?>

<?php if (isset($_GET["cancelled"])): ?>
    <p style="color: red; text-align: center;">❌ Booking cancelled successfully!</p>
<?php endif; ?>

</html>
