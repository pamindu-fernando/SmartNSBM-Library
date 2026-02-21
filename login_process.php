<?php
session_start();
include "db_config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $email = mysqli_real_escape_string($conn, $email);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        
        if (password_verify($password, $row['password'])) {

            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];

            echo "Login successful!";
            header("Location: main.php");
            exit();

        } else {
            echo "Wrong password!";
        }

    } else {
        echo "User not found!";
    }
}
?>
