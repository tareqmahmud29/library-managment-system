<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Login
if (isset($_POST['login'])) {
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    // Hardcoded admin credentials (for simplicity)
    if ($admin_username === 'admin' && $admin_password === 'admin123') {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        echo "<p style='color:red;'>Invalid credentials!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="sty.css">
</head>
<body>
    <!-- Background Particle Animation -->
    <div class="background">
        <div class="particle particle1"></div>
        <div class="particle particle2"></div>
        <div class="particle particle3"></div>
        <div class="particle particle4"></div>
        <div class="particle particle5"></div>
    </div>

    <div class="login-container">
        <div class="login-box">
            <h1>Admin Login</h1>
            <form method="post">
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
