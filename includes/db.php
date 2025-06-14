<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "gui-travel-tours";

$conn = new mysqli("localhost", "root", "", "gui-travel-tours");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
