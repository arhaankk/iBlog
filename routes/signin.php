<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); 

require '../database/db_config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["username"], $_POST["password"])) {
        header("Location: ../pages/signin.html?error=Invalid request. Please try again");
        exit;
    }
    
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        header("Location: ../pages/signin.html?error=Please fill out both fields");
        exit;
    }

    // Check if the username exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $connection->prepare($query);
    
    if ($stmt === false) {
        header("Location: ../pages/signin.html?error=Error preparing the query");
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // User does not exist
        header("Location: ../pages/signin.html?error=User not found. Feel free to register");
        exit;
    } else {
        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            // Incorrect password
            header("Location: ../pages/signin.html?error=Password is incorrect");
            exit;
        } else {
            // Successful login
            $_SESSION['username'] = $username;
            header("Location: ../pages/index.html"); 
            exit;
        }
    }

    $stmt->close();
} else {
    header("Location: ../pages/signin.html?error=Invalid request method");
    exit;
}
?>
