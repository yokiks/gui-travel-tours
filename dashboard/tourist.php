<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "tourist") {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$result = $conn->query("SELECT * FROM destinations");

// Fetch bookings for logged-in tourist
$userId = $_SESSION["user"]["id"];
$bookings = $conn->query("
    SELECT b.id, b.booking_reference, d.name AS destination_name, d.price, b.booking_date, b.status, b.payment_method, b.payment_status
    FROM bookings b
    JOIN destinations d ON b.destination_id = d.id
    WHERE b.user_id = $userId
    ORDER BY b.booking_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tourist Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7;
            padding: 20px;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        header a {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #dc3545;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
        }
        .destinations {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .card {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 250px;
            text-align: center;
        }
        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
        }
        .bookings {
            margin-top: 50px;
        }
        .booking-entry {
            background-color: #fff;
            padding: 15px;
            margin: 10px auto;
            border-radius: 8px;
            max-width: 1000px;
            box-shadow: 0 0 5px rgba(0,0,0,.1);
        }
        .booking-table {
            width: 100%;
            border-collapse: collapse;
        }
        .booking-table th, .booking-table td {
            text-align: left;
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
        .booking-table th {
            background-color: #f2f2f2;
        }
        .booking-entry form {
            margin-top: 5px;
        }
        .booking-entry button {
            padding: 6px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }
        .status {
            font-weight: bold;
        }
        .status.approved {
            color: green;
        }
        .status.cancelled {
            color: red;
        }
        .status.pending {
            color: orange;
        }
        .success-message {
            background: rgb(207, 23, 23);
            color: white;
            border: 1px solid rgb(187, 0, 0);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $_SESSION["user"]["fullname"]; ?></h1>
        <a href="../logout.php">Logout</a>
    </header>
        <a href="tourist-receipt.php" style="display:block;text-align:center;margin-top:20px;">🧾 View My Receipt</a>
    <?php if (isset($_GET['cancelled']) && $_GET['cancelled'] == 1): ?>
        <div class="success-message">
            You have successfully cancelled your booking.
        </div>
    <?php endif; ?>

    <main>
        <h2>Available Destinations</h2>
        <div class="destinations">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="../images/<?php echo $row['image']; ?>" alt="Destination">
                    <h3><?php echo $row['name']; ?></h3>
                    <p><?php echo $row['description']; ?></p>
                    <p><strong>₱<?php echo number_format($row['price'], 2); ?></strong></p>
                    <form action="../book.php" method="POST">
                        <input type="hidden" name="destination_id" value="<?php echo $row['id']; ?>">
                        <label for="payment_method">Payment Method:</label>
                        <select name="payment_method" required>
                            <option value="">-- Select --</option>
                            <option value="GCash">GCash</option>s
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash on Arrival">Cash on Arrival</option>
                        </select>
                        <button type="submit">Book Tour</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="bookings">
            <h2>My Bookings</h2>
            <?php if ($bookings->num_rows > 0): ?>
                <div class="booking-entry">
                    <table class="booking-table">
                        <thead>
                            <tr>
                                <th>Destination</th>
                                <th>Reference</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($b = $bookings->fetch_assoc()): ?>
                                <?php
                                    $statusClass = strtolower(str_replace(' ', '-', $b['status']));
                                    $statusColor = ($statusClass === 'cancelled-by-tourist' || $statusClass === 'cancelled') ? 'cancelled' :
                                                   (($statusClass === 'approved') ? 'approved' : 'pending');
                                ?>
                                <tr>
                                    <td><?php echo $b['destination_name']; ?></td>
                                    <td><?php echo $b['booking_reference']; ?></td>
                                    <td>₱<?php echo number_format($b['price'], 2); ?></td>
                                    <td><?php echo $b['booking_date']; ?></td>
                                    <td><?php echo $b['status']; ?></td>
                                    <td><?php echo $b['payment_method']; ?></td>
                                    <td><?php echo $b['payment_status']; ?></td>
                                    <td>
                                        <?php if ($b['status'] !== 'Cancelled' && $b['status'] !== 'Cancelled by Tourist' && $b['status'] !== 'Cancelled by Admin'): ?>
                                            <form action="cancel-booking.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($b['payment_status'] !== 'Paid' && $b['status'] === 'Pending'): ?>
                                            <form action="mark-paid.php" method="POST">
                                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                                <button class="cancel-btn" style="background-color: green; margin-top: 5px;">Mark as Paid</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No bookings yet.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>