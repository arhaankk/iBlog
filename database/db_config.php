<?php
$host = "localhost";
$user = "root"; 
$password = ""; 
$dbname = "iblog"; 

$connection = new mysqli($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
