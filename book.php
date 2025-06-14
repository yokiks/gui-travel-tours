<?php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "tourist") {
        header("Location: login.php");
        exit();
    }

    if (!isset($_POST['destination_id'], $_POST['payment_method'])) {
        echo "Incomplete booking data.";
        exit();
    }

    $userId = $_SESSION["user"]["id"];
    $destinationId = $_POST['destination_id'];
    $paymentMethod = $_POST['payment_method'];
    $bookingRef = uniqid('REF-');
    $bookingDate = date("Y-m-d");

    // Get destination details for receipt
    $destQuery = $conn->prepare("SELECT name, price FROM destinations WHERE id = ?");
    $destQuery->bind_param("i", $destinationId);
    $destQuery->execute();
    $destResult = $destQuery->get_result()->fetch_assoc();
    $destQuery->close();

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, destination_id, booking_reference, booking_date, status, payment_method, payment_status) VALUES (?, ?, ?, ?, 'Pending', ?, 'Unpaid')");
    $stmt->bind_param("iisss", $userId, $destinationId, $bookingRef, $bookingDate, $paymentMethod);

    if ($stmt->execute()) {
        $stmt->close();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Booking Confirmation</title>
            <link rel="stylesheet" href="css/style.css">
            <style>
                .receipt {
                    max-width: 500px;
                    margin: 80px auto;
                    background: #f9f9f9;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .receipt h2 {
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                <h2>Booking Confirmed!</h2>
                <p><strong>Destination:</strong> <?php echo htmlspecialchars($destResult["name"]); ?></p>
                <p><strong>Price:</strong> ₱<?php echo number_format($destResult["price"], 2); ?></p>
                <p><strong>Booking Reference:</strong> <?php echo $bookingRef; ?></p>
                <p><strong>Date:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>
                <a href="dashboard/tourist.php">Back to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    } else {
        echo "Booking failed. Please try again.";
        $stmt->close();
        exit();
    }
} else {
    header("Location: dashboard/tourist.php");
    exit();
}
?>