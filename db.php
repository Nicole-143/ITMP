<?php
// Database credentials
$servername = "localhost";
$username = "root"; 
$password = "123456"; 
$database = "brgydb"; 


$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>