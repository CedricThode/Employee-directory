<?php
// Database configuration
$host = 'localhost'; 
$dbname = 'employee-directory'; // Database name
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Error: Could not connect to the database. " . $e->getMessage());
}
?>
