<?php
/**
 * Shim to connect web configuration to legacy mysqli code.
 */
require_once('../util/IB.php');
$app = IB::app();
$db = $app->config('db');
$host = $db['host'];
$user = $db['user'];
$pass = $db['pass'];
$dbname = $db['database'];
try {
    $connection = new mysqli($host, $user, $pass, $dbname);
    if ($connection->connect_error) {
        //die("Connection failed: " . $conn->connect_error);
        $app->error($conn->connect_error, 'Database connection failed');
    }
} catch (mysqli_sql_exception $e) {
    $app->error($e->getMessage(), 'Database connection failed', $e->getTraceAsString());
}
?>
