<?php
function databaseConnection()
{
    $host = 'localhost'; // Use '127.0.0.1' if localhost fails
    $port = 3306;        // Default MySQL port
    $dbname = 'app';     // Replace with your database name
    $username = 'root';  // Replace with your database username
    $password = 'rootpassword'; // Replace with your database password (todo store pw securely)

    try {
        // Correct PDO DSN format
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
    return $pdo;
}