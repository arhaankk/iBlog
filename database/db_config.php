<?php
$host = "localhost";
$user = "root"; 
$pass = "password"; 
$dbname = "iblog"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
