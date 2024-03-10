<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "shopping"; 

// Creating connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else {
    echo "Connection successfully established!";
}
?>
