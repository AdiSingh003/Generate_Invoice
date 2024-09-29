<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$servername = $_ENV['DB_SERVER'];
$username = $_ENV['DB_USERNAME']; 
$password = $_ENV['DB_PASSWORD']; 
$database = $_ENV['DB_DATABASE'];
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
