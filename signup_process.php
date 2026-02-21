<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    if (strlen($username) < 6) {
        die("Error: Username must be at least 6 characters long.");
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/', $password)) {
        die("Error: Password must meet the security requirements.");
    }

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: signup.html?error=taken");
        exit();
    }
    
    $sql = "INSERT INTO users (fullname, username, password, role) VALUES ('$fullname', '$username', '$password', 'student')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: login.html?status=success");
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}
?>