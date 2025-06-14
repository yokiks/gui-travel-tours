<!DOCTYPE html>
<html>
<head>
    <title>Login - Travel & Tours</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 20px;
            background: #f7f7f7;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        input, select, button {
            width:100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <form action="process_login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin">Admin</option>
                <option value="agent">Agent</option>
                <option value="tourist">Tourist</option>
            </select>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
