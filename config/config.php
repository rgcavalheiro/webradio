<?php
$host = 'localhost';
$username = 'webradio';
$password = 'I6oLFzQZIi)W5GI0';
$database = 'webradio';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
