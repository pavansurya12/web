<?php
$servername = "localhost";
$username = "root";  // Change if using a different database user
$password = "";      // Set your MySQL password
$database = "quiz_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
