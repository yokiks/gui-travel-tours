<?php
session_start();

// If user is logged in, redirect to their dashboard
if (isset($_SESSION["user"])) {
    $role = $_SESSION["user"]["role"];
    if ($role === "admin") {
        header("Location: dashboard/admin.php");
    } elseif ($role === "agent") {
        header("Location: dashboard/agent.php");
    } elseif ($role === "tourist") {
        header("Location: dashboard/tourist.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Travel & Tours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #c9d6ff, #e2e2e2);
            text-align: center;
            padding: 100px 20px;
        }
        header h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        nav {
            margin-bottom: 30px;
        }
        nav a {
            margin: 0 10px;
            text-decoration: none;
            background-color: #0066cc;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
        }
        nav a:hover {
            background-color: #005bb5;
        }
        main p {
            font-size: 1.2rem;
            color: #444;
        }
    </style>
</head>
<body>
    <header>
        <h1>Online Travel & Tours Management System</h1>
        <nav>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>
    <main>
        <p>Welcome! Book your next adventure with ease. Login or register to continue.</p>
    </main>
</body>
</html>
