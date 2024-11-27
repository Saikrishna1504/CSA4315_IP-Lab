db.php

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "example"; 
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

registration.php

<?php
include 'db.php';
$message = ""; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";
    if ($conn->query($sql) === TRUE) {
        $message = "Registration successful!";
    } else {
        $message = "Error: " . $conn->error;
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Register</h2>
    <form method="POST" action="">
        <label>Full Name:</label><br>
        <input type="text" name="name" required><br><br>
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>    
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>    
        <button type="submit">Register</button>
    </form>
    <?php if (!empty($message)) { echo "<p>$message</p>"; } ?>
    <p>
        Already have an account? <a href="login.php">Login here</a>
    </p>
</body>
</html>


login.php

<?php
include 'db.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header("Location: home.php");
        exit();
    } else {
        $message = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>  
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>  
        <button type="submit">Login</button>
    </form>
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    <p>Don't have an account? <a href="registration.php">Register here</a></p>
</body>
</html>


home.php

<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Book Shop</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Welcome to Book Shop!</h1>
    <p>Explore our wide range of books and find something you'll love!</p>
    <a href="logout.php">Logout</a>
</body>
</html>

styles.css

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
    color: #333;
    line-height: 1.6;
}

.container {
    max-width: 800px;
    margin: 30px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

h2 {
    color: #555;
}


button, a {
    display: inline-block;
    text-decoration: none;
    background-color: #007BFF;
    color: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover, a:hover {
    background-color: #0056b3;
}

form {
    margin-top: 20px;
}

input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

button {
    width: 100%;
}


a {
    text-align: center;
    margin-top: 10px;
    display: block;
    font-size: 14px;
    color: #007BFF;
}

a:hover {
    text-decoration: underline;
}

p {
    text-align: center;
    font-size: 16px;
    margin-top: 20px;
}

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
